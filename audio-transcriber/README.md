# Audio Transcriber Plugin

This WordPress plugin creates a page where visitors can drag and drop an audio file to obtain a transcription.

## Features

- Automatically creates a page `Audio Transcriber` on activation.
- Drag & drop upload interface using AJAX.
- Invokes a Python script (`transcriber.py`) that attempts to transcribe the audio using the `openai-whisper` library. If Whisper is not available, it falls back to the `speech_recognition` library.

## Installation

1. Copy the `audio-transcriber` directory into your WordPress `wp-content/plugins` folder.
2. Ensure Python 3 with the required libraries (`openai-whisper` and `SpeechRecognition`) is available on the server.
   You can install them with `pip install openai-whisper SpeechRecognition`.
3. Activate the plugin from the WordPress admin area.
4. Visit the newly created page `Audio Transcriber` to use the transcriber.
