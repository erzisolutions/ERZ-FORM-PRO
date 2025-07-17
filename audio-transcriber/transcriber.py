import sys
import json

try:
    import whisper
except ImportError:
    whisper = None

try:
    import speech_recognition as sr
except ImportError:
    sr = None

def transcribe_whisper(path):
    model = whisper.load_model("base")
    result = model.transcribe(path)
    return result.get("text", "")

def transcribe_sr(path):
    r = sr.Recognizer()
    with sr.AudioFile(path) as source:
        audio = r.record(source)
    return r.recognize_google(audio)

def main():
    if len(sys.argv) < 2:
        print("No file provided", file=sys.stderr)
        sys.exit(1)
    path = sys.argv[1]
    text = ""
    if whisper:
        try:
            text = transcribe_whisper(path)
        except Exception as e:
            text = str(e)
    if not text and sr:
        try:
            text = transcribe_sr(path)
        except Exception as e:
            text = str(e)
    print(text)

if __name__ == "__main__":
    main()
