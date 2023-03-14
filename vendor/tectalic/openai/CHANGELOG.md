# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.4.0 - 2023-03-02

### Added
- Add support for the new **ChatGPT** API, including `gpt-3.5-turbo` model and the new Chat completions endpoint. [Chat completions guide](https://platform.openai.com/docs/guides/chat).
- Add support for the new **Whisper** API, allowing **Transcriptions** and **Translations**, accepting a variety of formats (`m4a`, `mp3`, `mp4`, `mpeg`, `mpga`, `wav`, `webm`). [Speech to text guide](https://platform.openai.com/docs/guides/speech-to-text).
- Add new `AudioTranscriptions` Handler, which transcribes audio into the input language text using the Whisper API.
- Add new `AudioTranslations` Handler, which transcribes audio into english text using the Whisper API.
- Add new `ChatCompletions` Handler, which creates a completion for one or more chat messages using the ChatGPT API.

### Changed
- Clarify which models can be used in `\Tectalic\OpenAi\Models\Edits\CreateRequest::$model` when performing Edits.
- Clarify that `\Tectalic\OpenAi\Models\Embeddings\CreateRequest:$input` can be a maximum of 8192 tokens (not 2048 tokens).
- Clarify that `\Tectalic\OpenAi\Models\ImagesEdits\CreateImageRequest::$mask` is no longer a required field.
- API version updated from 1.1.0 to 1.2.0.

## 1.3.1 - 2023-02-23

### Added
- Add support for PHPUnit v9.6.x and v10.x.

### Changed
- Remove `id` and `model` required properties from the `Tectalic\OpenAi\Models\Edits\CreateResponse` model, as they are no longer returned by OpenAI's API.
- Improve compatibility with the `php-http/discovery` package v1.15.0 and newer.
- Use Fully Qualified Class Names for Examples in Readme.
- Update Copyright year.

### Fixed
- Fix `Response body parse failed` error when retrieving a Model response from the `Edits::create()` handler and method.
- Fix incorrect Error handling example in Readme.

## 1.3.0 - 2022-12-21

### Added
- Use parameters defined outside endpoint methods.

### Changed
- Encourage the use of `php-http/mock-client` for testing and mocking API responses.
- Remove the `Tests\MockHttpClient` class, and use the `php-http/mock-client` package instead.
- Make Handler and Model class names more readable.

### Fixed
- Use correct model type for nested models.

## 1.2.0 - 2022-11-07

### Added
- Add support for DALL·E [image generation](https://beta.openai.com/docs/guides/images).
- Add new `ImageGenerations` Handler, which creates an image given a prompt.
- Add new `ImagesEdits` Handler, which creates an edited or extended image given an original image and a prompt.
- Add new `ImagesVariations` Handler, which creates a variation of a given image.

### Changed
- Improve Handler unit tests.
- API version updated from 1.0.6 to 1.1.0.

## 1.1.0 - 2022-10-31

### Changed
- Improve readme.

### Removed
- Remove deprecated `Answers` handler and associated models.
- Remove deprecated `Classifications` handler and associated models.
- Remove deprecated `Engines` handler and associated models.
- Remove deprecated `EnginesSearch` handler and associated models.

## 1.0.2 - 2022-10-28

### Changed
- Switch License.

## 1.0.1 - 2022-10-24

### Added
- Add support for [Moderation](https://beta.openai.com/docs/guides/moderation) using a new `Moderations::create()` Handler class and Method.
- Add [usage information](https://community.openai.com/t/usage-info-in-api-responses/18862) to response models: `Completions::create()`, `Edits::create()` and `Embeddings::create()`.

### Changed
- Define required properties for response models.
- Rename all nested response models.
- Change default value for `Tectalic\OpenAi\Models\FineTunes\CreateRequest::$prompt_loss_weight`.
- 22 API Methods are now supported, grouped into 14 API Handlers.
- API version updated from 1.0.5 to 1.0.6.

### Fixed
- Don't run CI for tags.
- Use correct model type for nested models: `Tectalic\OpenAi\Models\FineTunes\CreateResponse`, `Tectalic\OpenAi\Models\FineTunes\RetrieveResponse` and `Tectalic\OpenAi\Models\FineTunesCancel\CancelFineTuneResponse`.

## 1.0.0 - 2022-07-11

### Added
- Initial release.
