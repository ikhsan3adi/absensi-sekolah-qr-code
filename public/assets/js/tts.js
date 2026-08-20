/**
 * TTS engine untuk sistem absensi sekolah.
 *
 * Cara kerja:
 *  1. Kirim teks ke Edge-TTS API → putar audio MP3/WAV hasilnya.
 *  2. Bila server TTS tidak tersedia / timeout → fallback ke Web Speech API
 *     (browser built-in, tidak butuh server eksternal).
 *
 * Konfigurasi (opsional, set window.tts* SEBELUM file ini di-load):
 *   <script>
 *     window.ttsApiBase = 'http://localhost:8085';
 *     window.ttsVoice   = 'male';   // 'male' | 'female'
 *     window.ttsRate    = '-3%';
 *   </script>
 */

// ====== KONFIGURASI ======
var ttsApiBase   = (typeof window !== 'undefined' && window.ttsApiBase)   || 'http://localhost:8085';
var ttsApiKey    = (typeof window !== 'undefined' && window.ttsApiKey)    || '';
var ttsVoice     = (typeof window !== 'undefined' && window.ttsVoice)     || 'female';
var ttsRate      = (typeof window !== 'undefined' && window.ttsRate)      || '+0%';
var ttsLanguage  = (typeof window !== 'undefined' && window.ttsLanguage)  || 'indonesian';
var ttsTimeoutMs = (typeof window !== 'undefined' && window.ttsTimeoutMs) || 8000;

// ====== QUEUE (mencegah tumpang tindih audio) ======
var _ttsQueue   = [];
var _ttsPlaying = false;

function _enqueueTTS(text) {
  _ttsQueue.push(text);
  if (!_ttsPlaying) _processTTSQueue();
}

function _processTTSQueue() {
  if (_ttsQueue.length === 0) {
    _ttsPlaying = false;
    return;
  }
  _ttsPlaying = true;
  var text = _ttsQueue.shift();
  _requestEdgeTTS(text)
    .then(function () { _processTTSQueue(); })
    .catch(function (err) {
      console.warn('[tts] Edge-TTS gagal, fallback ke Web Speech API:', err);
      _speakWebSpeech(text, function () { _processTTSQueue(); });
    });
}

// ====== EDGE-TTS (server) ======
function _requestEdgeTTS(text) {
  return new Promise(function (resolve, reject) {
    var ctrl  = typeof AbortController === 'function' ? new AbortController() : null;
    var timer = setTimeout(function () {
      if (ctrl) ctrl.abort();
      else reject(new Error('timeout'));
    }, ttsTimeoutMs);

    var headers = { 'Content-Type': 'application/json' };
    if (ttsApiKey) headers['X-API-Key'] = ttsApiKey;

    fetch(ttsApiBase + '/tts', {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({ text: text, voice: ttsVoice, rate: ttsRate, language: ttsLanguage }),
      signal: ctrl ? ctrl.signal : undefined
    })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(function (data) {
        if (!data || !data.success || !data.audio_url) {
          throw new Error('respons TTS tidak valid');
        }
        var audio = new Audio(ttsApiBase + data.audio_url);
        audio.onended = function () { clearTimeout(timer); resolve(); };
        audio.onerror = function () { clearTimeout(timer); reject(new Error('audio error')); };
        var p = audio.play();
        if (p && typeof p.then === 'function') {
          p.catch(function (e) { clearTimeout(timer); reject(e); });
        }
      })
      .catch(function (err) { clearTimeout(timer); reject(err); });
  });
}

// ====== WEB SPEECH API (fallback) ======
function _speakWebSpeech(text, onDone) {
  if (!window.speechSynthesis) {
    console.warn('[tts] Web Speech API tidak didukung browser ini.');
    if (typeof onDone === 'function') onDone();
    return;
  }
  // Cancel utterance sebelumnya bila ada
  window.speechSynthesis.cancel();

  var utt  = new SpeechSynthesisUtterance(text);
  utt.lang = 'id-ID';
  utt.rate = 0.95;

  // Coba pilih suara bahasa Indonesia
  var voices = window.speechSynthesis.getVoices();
  var idVoice = voices.find(function (v) { return v.lang === 'id-ID'; });
  if (idVoice) utt.voice = idVoice;

  utt.onend   = function () { if (typeof onDone === 'function') onDone(); };
  utt.onerror = function () { if (typeof onDone === 'function') onDone(); };
  window.speechSynthesis.speak(utt);
}

// ====== PUBLIC API ======

/**
 * Ucapkan pemberitahuan absensi.
 *
 * @param {string} nama   - Nama siswa atau guru
 * @param {string} waktu  - 'masuk' | 'pulang'
 *
 * Contoh: speakAbsensi('Budi Santoso', 'masuk')
 * → "Budi Santoso, absen masuk berhasil."
 */
function speakAbsensi(nama, waktu) {
  if (!nama) return;
  var w    = (waktu || '').toLowerCase();
  var teks = nama.trim() + ', absen ' + (w === 'pulang' ? 'pulang' : 'masuk') + ' berhasil.';
  _enqueueTTS(teks);
}

/**
 * Ucapkan teks bebas (untuk keperluan lain).
 * @param {string} text
 */
function speakTTS(text) {
  if (!text) return;
  _enqueueTTS(text);
}
