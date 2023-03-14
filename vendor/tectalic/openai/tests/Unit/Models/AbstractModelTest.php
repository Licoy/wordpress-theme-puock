<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tests\Unit\Models;

use Exception;
use PHPUnit\Framework\TestCase;
use Spatie\DataTransferObject\DataTransferObjectError;
use Tectalic\OpenAi\Models\AbstractModel;

final class AbstractModelTest extends TestCase
{
    public function testAbstractModelWithDeprecatedProperty(): void
    {
        \set_error_handler(static function (int $errno, string $errstr) {
            throw new Exception($errstr, $errno);
        }, \E_USER_DEPRECATED);
        $this->expectExceptionMessage('Property `a` is deprecated');

        $class = new class (['a' => 'valueA', 'b' => []]) extends AbstractModel {
            protected const DEPRECATED = ['a'];
            /** @var string */
            public $a;
            /** @var array */
            public $b;
        };

        $this->assertSame('valueA', $class->a);
        $this->assertSame([], $class->b);
    }

    public function testAbstractModelWithExtraPropertyWhenNotFlexible(): void
    {
        $this->expectException(DataTransferObjectError::class);
        $this->expectExceptionMessageMatches('/^Public properties `a` not found/');

        $class = new class (['a' => 'valueA']) extends AbstractModel {
            protected $ignoreMissing = false;
        };
    }

    public function testAbstractModelWithInvalidType(): void
    {
        $this->expectException(DataTransferObjectError::class);
        $this->expectExceptionMessageMatches('/a` to be of type `string`, instead got value `123`, which is integer.$/');

        $class = new class (['a' => 123]) extends AbstractModel {
            /** @var string */
            public $a;
        };

        $this->assertSame('valueA', $class->a);
    }

    public function testAbstractModelWithDeprecatedPropertyNotSpecified(): void
    {
        $class = new class (['b' => []]) extends AbstractModel {
            protected const DEPRECATED = ['a'];
            /** @var string|null */
            public $a;
            /** @var array */
            public $b;
        };

        $this->assertFalse(isset($class->a));
        $this->assertSame([], $class->b);
    }

    public function testAbstractModelWithMappedProperty(): void
    {
        $class = new class (['full name' => 'valueA']) extends AbstractModel {
            protected const MAPPED = ['fullName' => 'full name'];
            /** @var string */
            public $fullName;
        };

        $this->assertFalse(property_exists($class, 'full name'));
        $this->assertSame('valueA', $class->fullName);
        $this->assertSame('{"full name":"valueA"}', \json_encode($class));
    }

    public function testAbstractModelWithSuppliedRequiredProperty(): void
    {
        $class = new class (['a' => 'valueA']) extends AbstractModel {
            protected const REQUIRED = ['a'];
            /** @var string */
            public $a;
        };

        $this->assertSame('valueA', $class->a);
        $this->assertSame('{"a":"valueA"}', \json_encode($class));
    }

    public function testAbstractModelWithMissingRequiredProperty(): void
    {
        $this->expectException(DataTransferObjectError::class);
        $this->expectExceptionMessageMatches('/a` has not been initialized./');

        $class = new class (['b' => 'valueB']) extends AbstractModel {
            protected const REQUIRED = ['a'];
            /** @var string */
            public $a;
            /** @var string */
            public $b;
        };
    }

    public function testAbstractModelWithSuppliedRequiredMappedProperty(): void
    {
        $class = new class (['full name' => 'valueA']) extends AbstractModel {
            protected const REQUIRED = ['full name'];
            protected const MAPPED = ['fullName' => 'full name'];
            /** @var string */
            public $fullName;
            /** @var string */
            public $b;
        };

        $this->assertFalse(property_exists($class, 'full name'));
        $this->assertSame('valueA', $class->fullName);
        $this->assertSame('{"full name":"valueA"}', \json_encode($class));
    }
}
