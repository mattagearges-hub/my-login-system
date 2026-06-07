// ===== Sidebar Navigation =====
function switchSection(section) {
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item[data-section]').forEach(n => n.classList.remove('active'));

  const sec = document.getElementById('section-' + section);
  if (sec) sec.classList.add('active');

  const nav = document.querySelector(`.nav-item[data-section="${section}"]`);
  if (nav) nav.classList.add('active');

  // Close sidebar on mobile
  document.getElementById('sidebar')?.classList.remove('open');
  document.getElementById('sidebarOverlay')?.classList.remove('active');

  // Load vocab when switching to dashboard
  if (section === 'dashboard') loadVocab();
}

function toggleSidebar() {
  document.getElementById('sidebar')?.classList.toggle('open');
  document.getElementById('sidebarOverlay')?.classList.toggle('active');
}

// ===== Theme =====
(function initTheme() {
  const saved = localStorage.getItem('theme') || 'dark';
  document.documentElement.setAttribute('data-theme', saved);
  updateToggleUI(saved);
})();

function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
  updateToggleUI(next);
}

function updateToggleUI(theme) {
  const track = document.getElementById('themeToggle');
  if (track) track.classList.toggle('active', theme === 'light');
}

// ===== Level Config =====
const levelConfig = {
  a1: { name: "A1 — الأساس", emoji: "🌱", duration: 3, days: 90, hours: 180 },
  a2: { name: "A2 — البناء", emoji: "🌿", duration: 4, days: 120, hours: 240 },
  b1: { name: "B1 — الاستقلال", emoji: "🌳", duration: 5, days: 150, hours: 300 },
  b2: { name: "B2 — الإتقان", emoji: "🌲", duration: 6, days: 180, hours: 360 },
  c1: { name: "C1 — التخصص", emoji: "🏔️", duration: 8, days: 240, hours: 480 }
};
const levelOrder = ['a1', 'a2', 'b1', 'b2', 'c1'];

// ===== Level Data (unchanged) =====
const levelData = {"a1":{"en":[{"title":"📖 Blue Level — قواعد أساسية","desc":"LearnAmericanEnglishOnline — قواعد + تدريبات","duration":"45 د","link":"https://www.learnamericanenglishonline.com/Blue%20Level/Blue%20Level.html"},{"title":"🎧 English Shadowing A1","desc":"تقليد صوتي يومي — Level A1","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLz-R9dJCp1RLGT9qtJDxyswAyAoemW86b"},{"title":"🗣️ Pronunciation Lessons","desc":"أصوات American الأساسية","duration":"20 د","link":"https://www.youtube.com/playlist?list=PLKWcPfZiScgAutnWOSAh2AH026L9w8c18"},{"title":"📱 Anki — 10 كلمات يومياً","desc":"مفردات يومية + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 VOA Learning English","desc":"استماع بطيء وواضح","duration":"25 د","link":"https://learningenglish.voanews.com/p/5644.html"},{"title":"📝 American English File Starter","desc":"كتاب Oxford الرسمي","duration":"30 د","link":"https://elt.oup.com/student/americanenglishfile/starter/?cc=eg&selLanguage=en"}],"de":[{"title":"📖 DW Nicos Weg — A1","desc":"أفضل مصدر مجاني للمبتدئين","duration":"60 د","link":"https://learngerman.dw.com/ar/%D9%85%D9%8F%D8%A8%D8%AA%D8%AF%D8%A6/s-62611666"},{"title":"🎧 German Shadowing A1","desc":"تقليد صوتي — A1","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLrCHdoh07TXMS1l4hou7yO9He1k9Ov8Y1"},{"title":"🗣️ تعلم القراءة وتحسين النطق","desc":"نطق Hochdeutsch","duration":"20 د","link":"https://www.youtube.com/playlist?list=PLtrxY2_KUcoGLH5wwBK1h8pG4CMQ7EtXu"},{"title":"📱 Anki — 15 كلمة يومياً","desc":"مفردات ألمانية + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 Super Easy German","desc":"قواعد + مفردات بطيئة","duration":"20 د","link":"https://www.youtube.com/playlist?list=PLk1fjOl39-53GxQIn1Hxdouokf0J0SDpl"},{"title":"📝 كورس A1.1 + A1.2","desc":"YouTube — إكمال المسار","duration":"30 د","link":"https://www.youtube.com/playlist?list=PL_Lje8xtkLIrDBN78pbFG5r1xFOIHXIBj"}]},"a2":{"en":[{"title":"📖 Yellow Level — قواعد متوسطة","desc":"LearnAmericanEnglishOnline","duration":"45 د","link":"https://www.learnamericanenglishonline.com/Yellow%20Level/Yellow%20Level.html"},{"title":"🎧 English Shadowing A2","desc":"تقليد صوتي يومي","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLz-R9dJCp1RIoowpJWm3mE1n8Ujjo1_jV"},{"title":"🗣️ Master The Sounds of American English","desc":"أصوات أمريكية متقدمة","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLB043E64B8BE05FB7"},{"title":"📱 Anki — 15 كلمة يومياً","desc":"مفردات + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 Randall's ESL Lab — Easy","desc":"استماع تفاعلي","duration":"25 د","link":"https://www.esl-lab.com/easy/"},{"title":"📝 American English File Level 1+2","desc":"Oxford — إكمال A2","duration":"30 د","link":"https://elt.oup.com/student/americanenglishfile/level01/?cc=eg&selLanguage=en"}],"de":[{"title":"📖 DW Nicos Weg — A2","desc":"استكمال المسار","duration":"45 د","link":"https://learngerman.dw.com/ar/%D9%85%D9%8F%D8%A8%D8%AA%D8%AF%D8%A6/s-62611666"},{"title":"🎧 A2-B1 Shadowing Series","desc":"تقليد متوسط","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLrCHdoh07TXOm5dCQm0CV7SO0X-OrK1_k"},{"title":"🗣️ تعلم المحادثة الألمانية","desc":"مواقف حقيقية","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLtrxY2_KUcoG_1lgp3aaerFHIlWzCwV4t"},{"title":"📱 Anki — 20 كلمة يومياً","desc":"مفردات + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 Coffee Break German S2","desc":"بودكاست تعليمي","duration":"25 د","link":"https://coffeebreaklanguages.com/tag/cbg-season-2/?order=asc"},{"title":"📝 كورس A2.1 + A2.2","desc":"YouTube — إكمال A2","duration":"30 د","link":"https://www.youtube.com/playlist?list=PL_Lje8xtkLIqpCrCKBFmSelYz7pN-S7XN"}]},"b1":{"en":[{"title":"📖 Green + Purple Level","desc":"قواعد متقدمة","duration":"40 د","link":"https://www.learnamericanenglishonline.com/Green%20Level/Green%20Level.html"},{"title":"🎧 English Shadowing B1","desc":"تقليد يومي متقدم","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLz-R9dJCp1RKqoz3WDWAfK2WXnH8pWa4K"},{"title":"🗣️ Phrasal Verb Lessons","desc":"أهم 200 phrasal verb","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLKWcPfZiScgBACqEFo5TuhJWAReRNNTc8"},{"title":"📱 Anki — 25 كلمة يومياً","desc":"مفردات + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 All Ears English Podcast","desc":"محادثات أمريكية طبيعية","duration":"25 د","link":"https://www.allearsenglish.com/"},{"title":"📝 American English File Level 3","desc":"Oxford B1","duration":"30 د","link":"https://elt.oup.com/student/americanenglishfile/level03/?cc=eg&selLanguage=en"}],"de":[{"title":"📖 DW Nicos Weg — B1","desc":"المسار المتقدم","duration":"45 د","link":"https://learngerman.dw.com/de/fortgeschrittene/s-62079033"},{"title":"🎧 Slow German Podcast","desc":"Hochdeutsch بطيء","duration":"30 د","link":"https://slowgerman.com/en/"},{"title":"🗣️ الألمانية العامية من الشارع","desc":"لهجة الشارع","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLtrxY2_KUcoHuth-IPq3QHPuOvD6nAyAo"},{"title":"📱 Anki — 25 كلمة يومياً","desc":"مفردات + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 Easy German Podcast","desc":"محادثات حقيقية","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLk1fjOl39-52AXeDQz2WPVv-wZnkjldoX"},{"title":"📝 Goethe B1 Exam Training","desc":"نماذج رسمية مجانية","duration":"30 د","link":"https://www.goethe.de/en/spr/prf/ueb/pb1.html"}]},"b2":{"en":[{"title":"📖 Orange + Violet Level","desc":"قواعد B2","duration":"35 د","link":"https://www.learnamericanenglishonline.com/Orange%20Level/Orange%20Level.html"},{"title":"🎧 English Shadowing B2","desc":"تقليد متقدم","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLz-R9dJCp1RLPBieaFe-urHZJP_mb3xun"},{"title":"🗣️ THINK IN ENGLISH","desc":"التفكير بالإنجليزي","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLrqHrGoMJdTTQbVnR6xs78gMLfJp4U7IT"},{"title":"📱 Anki — 30 كلمة يومياً","desc":"مفردات + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 ELLLO B2","desc":"محادثات متقدمة","duration":"25 د","link":"https://elllo.org/levels/B2-English-Lessons/index.html"},{"title":"📝 4000 Words Book 4","desc":"30 كلمة يومياً","duration":"30 د","link":"https://teracourses.com/ar/courses/4000-essential-english-words-book-4"}],"de":[{"title":"📖 DW Nicos Weg — B2","desc":"المسار المتقدم","duration":"45 د","link":"https://learngerman.dw.com/de/fortgeschrittene/s-62079033"},{"title":"🎧 DW Nachrichten (Langsam)","desc":"أخبار بطيئة يومية","duration":"30 د","link":"https://www.dw.com/de/deutsch-lernen/nachrichten/s-8030"},{"title":"🗣️ Goethe B2 Exam Training","desc":"نماذج رسمية","duration":"30 د","link":"https://www.goethe.de/en/spr/prf/ueb/pb2.html"},{"title":"📱 Anki — 30 كلمة يومياً","desc":"مفردات + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 Easy German Vlogs","desc":"فيديوهات يومية","duration":"25 د","link":"https://www.youtube.com/playlist?list=PLk1fjOl39-50RITUTsTOLTnBoTRlxwroe"},{"title":"📝 German B2 | Upper Intermediate","desc":"محتوى B2","duration":"30 د","link":"https://www.youtube.com/playlist?list=PLk1fjOl39-51lvdiuQYsLW-0aGIdNNknA"}]},"c1":{"en":[{"title":"🔧 Maintenance — All Ears Podcast","desc":"استماع يومي — صيانة B2","duration":"20 د","link":"https://www.allearsenglish.com/"},{"title":"🔧 Shadowing B2","desc":"تقليد صوتي — صيانة","duration":"15 د","link":"https://www.youtube.com/playlist?list=PLz-R9dJCp1RLPBieaFe-urHZJP_mb3xun"},{"title":"🔧 Netflix / Language Reactor","desc":"مسلسلات أمريكية — صيانة","duration":"15 د","link":"https://www.languagereactor.com/c/en/yt/t_yt_mix_en"},{"title":"🔧 Anki Review","desc":"مراجعة 20 كلمة — صيانة","duration":"10 د","link":"https://apps.ankiweb.net/"}],"de":[{"title":"📖 DW — Profis (C1)","desc":"المسار الاحترافي","duration":"45 د","link":"https://learngerman.dw.com/de/profis/s-62079037"},{"title":"🎧 Tagesschau","desc":"أخبار ألمانية أصلية","duration":"35 د","link":"https://www.tagesschau.de/"},{"title":"🗣️ Goethe C1 Exam Training","desc":"نماذج رسمية C1","duration":"30 د","link":"https://www.goethe.de/en/spr/prf/ueb/pc1.html"},{"title":"📱 Anki — 40 كلمة يومياً","desc":"مفردات C1 + مراجعة","duration":"30 د","link":"https://apps.ankiweb.net/"},{"title":"📺 DW Nachrichten (Normal)","desc":"أخبار بسرعة طبيعية","duration":"30 د","link":"https://www.dw.com/de/deutsch-lernen/nachrichten/s-8030"},{"title":"📝 Der Spiegel / Die Zeit","desc":"مقالات متعمقة","duration":"30 د","link":"https://www.spiegel.de/"},{"title":"🎯 Konjunktiv I & II","desc":"قواعد C1 المتقدمة — SmarterGerman","duration":"30 د","link":"https://smartergerman.com/courses/advanced-german-c1/"},{"title":"📚 Aspekte C1 Lehrbuch","desc":"كتاب C1 الرسمي — Cornelsen","duration":"30 د","link":"https://www.cornelsen.de/"}]}};

const thursdayExam = {
  title: "📋 يوم الخميس — مراجعة + اختبار أسبوعي",
  emoji: "📝",
  sections: [
    { title: "🇩🇪 مراجعة مفردات الأسبوع (ألماني)", desc: "راجع كل الكلمات الجديدة اللي خدتها الأسبوع ده في Anki. حاول تستخدمهم في جمل.", duration: "30 دقيقة", link: "https://apps.ankiweb.net/" },
    { title: "🇺🇸 مراجعة مفردات الأسبوع (إنجليزي)", desc: "راجع كل الكلمات الجديدة في Anki / Memrise. اكتب 5 جمل بكل كلمة.", duration: "30 دقيقة", link: "https://apps.ankiweb.net/" },
    { title: "🎤 Shadowing Test — تسجيل صوتك", desc: "سجّل نفسك وأنت بتقرأ نص من المستوى الحالي. قارن بالأصل.", duration: "20 دقيقة", link: "#" },
    { title: "✍️ Writing Challenge", desc: "اكتب فقرة 100 كلمة عن موضوع عشوائي (تكنولوجيا، سفر، عيلتك).", duration: "30 دقيقة", link: "#" },
    { title: "📝 اختبار أسبوعي — اختيار من متعدد", desc: "اختبر نفسك على القواعد والمفردات.", duration: "30 دقيقة", links: [{ name: "Cambridge Test", url: "https://www.cambridgeenglish.org/test-your-english/" }, { name: "Goethe Test", url: "https://www.goethe.de/en/spr/prf/ueb.html" }, { name: "DW Test", url: "https://learngerman.dw.com/ar/%D8%AA%D8%B9%D9%84%D9%8F%D9%91%D9%85-%D8%A7%D9%84%D8%A3%D9%84%D9%85%D8%A7%D9%86%D9%8A%D8%A9/s-9224" }] }
  ]
};
const fridayRest = {
  title: "⛪ يوم الجمعة — إجازة وراحة",
  emoji: "⛪",
  sections: [
    { title: "🧘 راحة كاملة", desc: "يوم راحة من المذاكرة المكثفة. خلّي دماغك يرتاح.", duration: "—", link: null },
    { title: "📖 قراءة خفيفة (اختياري)", desc: "لو حابب، اقرأ حاجة خفيفة بالإنجليزي أو الألماني.", duration: "15 د (اختياري)", link: null },
    { title: "🎯 تجهيز للأسبوع الجاي", desc: "خطّط للأسبوع الجاي. شوف المواضيع اللي هتدرسها.", duration: "10 د (اختياري)", link: null }
  ]
};
const dayNames = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];

// Thursday tasks split by language
const thursdayEnglish = {
  sections: [
    { title: "🇺🇸 مراجعة مفردات الأسبوع (إنجليزي)", desc: "راجع كل الكلمات الجديدة في Anki / Memrise. اكتب 5 جمل بكل كلمة.", duration: "30 د", link: "https://apps.ankiweb.net/" },
    { title: "🎤 Shadowing Test — تسجيل صوتك", desc: "سجّل نفسك وأنت بتقرأ نص إنجليزي من المستوى الحالي. قارن بالأصل.", duration: "20 د", link: "#" },
    { title: "✍️ Writing Challenge", desc: "اكتب فقرة 100 كلمة بالإنجليزي عن موضوع عشوائي.", duration: "30 د", link: "#" },
    { title: "📝 اختبار أسبوعي", desc: "اختبر نفسك على القواعد والمفردات الإنجليزية.", duration: "30 د", links: [{ name: "Cambridge Test", url: "https://www.cambridgeenglish.org/test-your-english/" }] }
  ]
};

const thursdayGerman = {
  sections: [
    { title: "🇩🇪 مراجعة مفردات الأسبوع (ألماني)", desc: "راجع كل الكلمات الجديدة اللي خدتها الأسبوع ده في Anki. حاول تستخدمهم في جمل.", duration: "30 د", link: "https://apps.ankiweb.net/" },
    { title: "📝 اختبار أسبوعي", desc: "اختبر نفسك على القواعد والمفردات الألمانية.", duration: "30 د", links: [{ name: "Goethe Test", url: "https://www.goethe.de/en/spr/prf/ueb.html" }, { name: "DW Test", url: "https://learngerman.dw.com/ar/%D8%AA%D8%B9%D9%84%D9%8F%D9%91%D9%85-%D8%A7%D9%84%D8%A3%D9%84%D9%85%D8%A7%D9%86%D9%8A%D8%A9/s-9224" }] }
  ]
};

// ===== State =====
let currentLevel = localStorage.getItem('currentLevel') || 'a1';
let completedTasks = JSON.parse(localStorage.getItem('completedTasks') || '{}');
let streak = parseInt(localStorage.getItem('streak') || '0');
let lastStudyDate = localStorage.getItem('lastStudyDate') || '';
let levelStudyDays = JSON.parse(localStorage.getItem('levelStudyDays') || '{"a1":0,"a2":0,"b1":0,"b2":0,"c1":0}');

// ===== Server Sync =====
async function syncToServer() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrf) return;
  try {
    await fetch('save_progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf_token: csrf, current_level: currentLevel, completed_tasks: completedTasks, streak, last_study_date: lastStudyDate, level_study_days: levelStudyDays })
    });
  } catch (e) {}
}

async function loadFromServer() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrf) return;
  try {
    const resp = await fetch('load_progress.php?csrf_token=' + encodeURIComponent(csrf));
    const data = await resp.json();
    if (data) {
      if (data.completed_tasks) { const s = JSON.parse(data.completed_tasks); completedTasks = { ...completedTasks, ...s }; localStorage.setItem('completedTasks', JSON.stringify(completedTasks)); }
      if (data.streak !== undefined && parseInt(data.streak) > streak) { streak = parseInt(data.streak); localStorage.setItem('streak', streak); }
      if (data.last_study_date) { lastStudyDate = data.last_study_date; localStorage.setItem('lastStudyDate', lastStudyDate); }
      if (data.level_study_days) { const s = JSON.parse(data.level_study_days); for (const k in s) { if (s[k] > (levelStudyDays[k] || 0)) levelStudyDays[k] = s[k]; } localStorage.setItem('levelStudyDays', JSON.stringify(levelStudyDays)); }
    }
  } catch (e) {}
}

// ===== Init =====
function init() {
  document.getElementById('levelSelect').addEventListener('change', (e) => {
    currentLevel = e.target.value;
    localStorage.setItem('currentLevel', currentLevel);
    renderAll();
  });
  updateLevelSelect();
  updateStreak();
  updateSidebarStreak();
  renderAll();
  loadFromServer().then(() => { renderAll(); updateSidebarStreak(); loadVocab(); });
  setTimeout(loadVocab, 500);
  document.getElementById('vocabInput')?.addEventListener('keydown', function(e) { if (e.key === 'Enter') saveVocab(); });
}

function updateLevelSelect() {
  const s = document.getElementById('levelSelect');
  if (s) s.value = currentLevel;
  const badge = document.getElementById('currentLevelNameSmall');
  if (badge) {
    const names = {a1:'🌱 A1',a2:'🌿 A2',b1:'🌳 B1',b2:'🌲 B2',c1:'🏔️ C1'};
    badge.textContent = names[currentLevel] || currentLevel.toUpperCase();
  }
}

const dailyQuotes = [
  "كل يوم بتتقدم خطوة — والاستمرارية هي السر 💪",
  "لغة جديدة = عالم جديد. كل كلمة بتفتحلك باب ✨",
  "مافيش حاجة اسمها 'مش قادر' — في حاجة اسمها 'لسه ما جربتش كفاية' 🔥",
  "العقول العظيمة بتتعلم لغات.. والعقول العظيمة بتبدأ من الصفر 🌟",
  "لما تتعلم لغة.. مش بتتعلم كلمات — بتتعلم ثقافة وأسلوب تفكير 🧠",
  "الموهبة وهم.. اللي بيفرق هو الـ 30 دقيقة اللي بتذاكرهم النهاردة 📚",
  "واثق الخطوة يمشي ملكاً 👑 — والثقة بتجيبها من المعرفة",
  "اللغة مش صعبة.. هي بس عادة بنكررها كل يوم 🎯",
  "اليوم الواحد 24 ساعة — خصص ساعتين لعقلك الباقي هيلحق كل حاجة ⏰",
  "تخيل نفسك بعد سنة من النهاردة — نفسك هتشكرك 💫"
];

function renderAll() {
  renderDay();
  updateLevelProgress();
  renderTimeline();
  renderLangSections();
  updateLangSectionLevels();
  setDailyQuote();
}

function setDailyQuote() {
  const el = document.getElementById('dailyQuote');
  if (!el) return;
  const dayOfYear = Math.floor((Date.now() - new Date(new Date().getFullYear(), 0, 0)) / 86400000);
  el.textContent = dailyQuotes[dayOfYear % dailyQuotes.length];

  const wisdom = document.getElementById('wisdomText');
  if (wisdom) {
    const wisdomQuotes = [
      "تعلّم لغة جديدة هو أن تصبح إنساناً آخر.. كل كلمة بتفتح لك عالم جديد",
      "Language is the road map of a culture. It tells you where its people come from and where they are going.",
      "Wer eine neue Sprache lernt, erwirbt eine neue Seele. — من يتعلم لغة جديدة يكتسب روحاً جديدة",
      "حدود عالمي هي حدود لغتي — Ludwig Wittgenstein",
      "A different language is a different vision of life. — Federico Fellini",
      "Die Grenzen meiner Sprache bedeuten die Grenzen meiner Welt.",
      "If you talk to a man in a language he understands, that goes to his head. If you talk to him in his language, that goes to his heart. — Nelson Mandela",
      "كل لغة تأخذك في رحلة لا تشبه أي رحلة أخرى.. استمتع بكل محطة 🚂"
    ];
    wisdom.textContent = wisdomQuotes[dayOfYear % wisdomQuotes.length];
  }
}

// ===== Level Progress =====
function updateLevelProgress() {
  const cfg = levelConfig[currentLevel];
  const d = levelStudyDays[currentLevel] || 0;
  const t = cfg.days;
  const p = Math.min(100, Math.round((d / t) * 100));
  document.getElementById('currentLevelName').textContent = cfg.name;
  document.getElementById('levelDaysStudied').textContent = d;
  document.getElementById('levelTotalDays').textContent = t;
  document.getElementById('levelPercentage').textContent = p + '%';
  const bar = document.getElementById('levelProgressBar');
  if (bar) { bar.style.width = p + '%'; bar.setAttribute('aria-valuenow', p); }
}

function updateLangSectionLevels() {
  const cfg = levelConfig[currentLevel];
  const en = document.getElementById('enSectionLevel');
  const de = document.getElementById('deSectionLevel');
  if (en) en.textContent = cfg.name;
  if (de) de.textContent = cfg.name;
}

// ===== Streak =====
function updateStreak() {
  const today = new Date().toDateString();
  const yesterday = new Date(Date.now() - 86400000).toDateString();
  if (lastStudyDate !== '' && lastStudyDate !== today && lastStudyDate !== yesterday) streak = 0;
  document.getElementById('streakCount').textContent = streak;
  updateSidebarStreak();
}

function updateSidebarStreak() {
  const sidebarStreak = document.getElementById('streakCountSidebar');
  if (sidebarStreak) sidebarStreak.textContent = streak;
}

// ===== Day Content =====
function renderDay() {
  const dayIdx = new Date().getDay();
  const dayName = dayNames[dayIdx];
  const isFri = dayIdx === 5;
  const isThu = dayIdx === 4;

  const display = document.getElementById('currentDayDisplay');
  if (display) display.textContent = dayName;

  const container = document.getElementById('dayContent');
  if (!container) return;

  if (isFri) renderFriday(container);
  else if (isThu) renderThursday(container, dayName);
  else renderStudyDay(container, dayName);

  updateProgress();
}

// ===== Study Day =====
function renderStudyDay(container, dayName) {
  const level = levelData[currentLevel];
  const cfg = levelConfig[currentLevel];
  const today = new Date().toDateString();
  const dayKey = today + '_' + currentLevel;

  let html = `<div class="day-header"><h2>${cfg.emoji} ${cfg.name}</h2><p style="margin-top:4px;">📅 ${dayName} — يوم دراسي</p><div class="day-tag">⏱️ ${cfg.duration} شهور | ${currentLevel === 'c1' ? '1h EN + 3h DE' : '2h EN + 2h DE'}</div></div>`;

  html += `<div class="tasks-section"><div class="section-title en">🇺🇸 English <span class="title-lang" id="enProgress">0/${level.en.length}</span></div>`;
  level.en.forEach((t, i) => { html += taskHTML(dayKey + '_en_' + i, t, completedTasks[dayKey + '_en_' + i] || false); });
  html += `</div>`;

  html += `<div class="tasks-section"><div class="section-title de">🇩🇪 Deutsch <span class="title-lang" id="deProgress">0/${level.de.length}</span></div>`;
  level.de.forEach((t, i) => { html += taskHTML(dayKey + '_de_' + i, t, completedTasks[dayKey + '_de_' + i] || false); });
  html += `</div>`;

  container.innerHTML = html;
  bindTasks();
  updateLangProgress();
}

function taskHTML(id, t, done) {
  const link = t.link ? `<a href="${t.link}" class="task-link">🔗 فتح</a>` : '';
  return `<div class="task-item ${done?'completed':''}" data-task-id="${id}" tabindex="0" role="button" aria-pressed="${done}"><div class="task-checkbox ${done?'checked':''}"></div><div class="task-content"><div class="task-title">${t.title}</div><div class="task-desc">${t.desc}</div></div><div class="task-duration">${t.duration}</div>${link}</div>`;
}

function bindTasks() {
  document.querySelectorAll('#dayContent .task-item').forEach(item => {
    const cb = function(e) { if (e.target.tagName === 'A' || e.target.closest('a')) return; toggleTask(this.dataset.taskId); };
    item.addEventListener('click', cb);
    item.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cb.call(this, e); } });
  });
}

// ===== Thursday =====
function renderThursday(container, dayName) {
  const today = new Date().toDateString();
  const dk = today + '_thursday';
  let html = `<div class="day-header thursday"><h2>${thursdayExam.emoji} ${thursdayExam.title}</h2><p style="margin-top:4px;">📅 ${dayName}</p><div class="day-tag">⏱️ 2-3 ساعات</div></div><div class="exam-section"><h3>📝 المهام</h3>`;
  thursdayExam.sections.forEach((s, i) => {
    const id = dk + '_' + i;
    const done = completedTasks[id] || false;
    html += `<div class="task-item ${done?'completed':''}" data-task-id="${id}" tabindex="0" role="button" aria-pressed="${done}"><div class="task-checkbox ${done?'checked':''}"></div><div class="task-content"><div class="task-title">${s.title}</div><div class="task-desc">${s.desc}</div></div><div class="task-duration">${s.duration}</div></div>`;
    if (s.links) { html += `<div style="margin-right: 44px; margin-bottom:12px;">`; s.links.forEach(l => { html += `<a href="${l.url}" class="exam-link">${l.name}</a>`; }); html += `</div>`; }
    else if (s.link) { html += `<div style="margin-right: 44px; margin-bottom:12px;"><a href="${s.link}" class="exam-link">فتح المصدر</a></div>`; }
  });
  html += `</div>`;
  container.innerHTML = html;
  document.querySelectorAll('#dayContent .task-item').forEach(item => {
    const cb = function(e) { if (e.target.tagName === 'A' || e.target.closest('a')) return; toggleTask(this.dataset.taskId); };
    item.addEventListener('click', cb);
    item.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cb.call(this, e); } });
  });
}

// ===== Friday =====
function renderFriday(container) {
  const today = new Date().toDateString();
  const dk = today + '_friday';
  let html = `<div class="day-header friday"><h2>${fridayRest.emoji} ${fridayRest.title}</h2><div class="day-tag" style="margin-top:8px;">⛪ يوم راحة</div></div><div class="rest-section"><div class="rest-emoji">⛪</div><div class="rest-message">يوم راحة من المذاكرة المكثفة</div>`;
  fridayRest.sections.forEach((s, i) => {
    const id = dk + '_' + i;
    const done = completedTasks[id] || false;
    html += `<div class="task-item ${done?'completed':''}" data-task-id="${id}" tabindex="0" role="button" aria-pressed="${done}"><div class="task-checkbox ${done?'checked':''}"></div><div class="task-content"><div class="task-title">${s.title}</div><div class="task-desc">${s.desc}</div></div><div class="task-duration">${s.duration}</div></div>`;
  });
  html += `</div>`;
  container.innerHTML = html;
  document.querySelectorAll('#dayContent .task-item').forEach(item => {
    const cb = function(e) { if (e.target.tagName === 'A' || e.target.closest('a')) return; toggleTask(this.dataset.taskId); };
    item.addEventListener('click', cb);
    item.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cb.call(this, e); } });
  });
}

// ===== Toggle Task =====
function toggleTask(id) {
  if (completedTasks[id]) delete completedTasks[id];
  else completedTasks[id] = true;
  localStorage.setItem('completedTasks', JSON.stringify(completedTasks));

  const el = document.querySelector(`[data-task-id="${id}"]`);
  if (el) {
    el.classList.toggle('completed');
    const cb = el.querySelector('.task-checkbox');
    if (cb) cb.classList.toggle('checked');
    el.setAttribute('aria-pressed', el.classList.contains('completed'));
  }
  updateProgress();
  updateLangProgress();
  checkCompletion();
  renderLangSections();
}

// ===== Progress =====
function updateProgress() {
  const today = new Date().toDateString();
  const dk = today + '_' + currentLevel;
  const isFri = new Date().getDay() === 5;
  const isThu = new Date().getDay() === 4;
  let total = 0, completed = 0, mins = 0;

  if (isFri) { total = fridayRest.sections.length; fridayRest.sections.forEach((_, i) => { if (completedTasks[today+'_friday_'+i]) { completed++; mins += parseInt(_.duration)||0; } }); }
  else if (isThu) { total = thursdayExam.sections.length; thursdayExam.sections.forEach((_, i) => { if (completedTasks[today+'_thursday_'+i]) { completed++; mins += parseInt(_.duration)||0; } }); }
  else {
    const lv = levelData[currentLevel];
    total = lv.en.length + lv.de.length;
    [...lv.en, ...lv.de].forEach((t, i) => {
      const lang = i < lv.en.length ? 'en' : 'de';
      const idx = i < lv.en.length ? i : i - lv.en.length;
      if (completedTasks[dk+'_'+lang+'_'+idx]) { completed++; mins += parseInt(t.duration)||0; }
    });
  }

  const pct = total > 0 ? Math.round((completed/total)*100) : 0;
  document.getElementById('completedTasks').textContent = completed;
  document.getElementById('totalTasks').textContent = total;
  document.getElementById('studyHours').textContent = (mins/60).toFixed(1);
  const s2 = document.getElementById('streakCount2');
  if (s2) s2.textContent = streak;

  const bar = document.getElementById('progressBar');
  const pctLabel = document.getElementById('progressPercent');
  if (bar) { bar.style.width = pct+'%'; bar.classList.toggle('complete', pct===100); bar.setAttribute('aria-valuenow', pct); }
  if (pctLabel) pctLabel.textContent = pct+'%';
}

// ===== Lang Progress =====
function updateLangProgress() {
  const today = new Date().toDateString(), dk = today+'_'+currentLevel;
  const dayIdx = new Date().getDay();
  const isFri = dayIdx === 5;
  const isThu = dayIdx === 4;

  if (isFri) return;

  if (isThu) {
    let ec=0, dc=0;
    thursdayEnglish.sections.forEach((_,i) => { if (completedTasks[today+'_thu_en_'+i]) ec++; });
    thursdayGerman.sections.forEach((_,i) => { if (completedTasks[today+'_thu_de_'+i]) dc++; });
    const ep = document.getElementById('enProgress'), dp = document.getElementById('deProgress');
    if (ep) ep.textContent = ec+'/'+thursdayEnglish.sections.length;
    if (dp) dp.textContent = dc+'/'+thursdayGerman.sections.length;
    return;
  }

  const lv = levelData[currentLevel];
  if (!lv) return;
  let ec=0, dc=0;
  lv.en.forEach((_,i) => { if (completedTasks[dk+'_en_'+i]) ec++; });
  lv.de.forEach((_,i) => { if (completedTasks[dk+'_de_'+i]) dc++; });
  const ep = document.getElementById('enProgress'), dp = document.getElementById('deProgress');
  if (ep) ep.textContent = ec+'/'+lv.en.length;
  if (dp) dp.textContent = dc+'/'+lv.de.length;
}

// ===== Check Completion =====
function checkCompletion() {
  const bar = document.getElementById('progressBar');
  if (!bar) return;
  const today = new Date().toDateString();

  if (bar.classList.contains('complete')) {
    if (lastStudyDate !== today) {
      streak++;
      lastStudyDate = today;
      localStorage.setItem('streak', streak);
      localStorage.setItem('lastStudyDate', lastStudyDate);
      document.getElementById('streakCount').textContent = streak;
      const ss = document.getElementById('streakCountSidebar');
      if (ss) ss.textContent = streak;
      if (!levelStudyDays[currentLevel]) levelStudyDays[currentLevel]=0;
      levelStudyDays[currentLevel]++;
      localStorage.setItem('levelStudyDays', JSON.stringify(levelStudyDays));
      updateLevelProgress();
    }
    document.getElementById('celebration')?.classList.add('active');
    syncToServer();
  }
}

function closeCelebration() { document.getElementById('celebration')?.classList.remove('active'); }
function closeUnlockModal() { document.getElementById('unlockModal')?.classList.remove('active'); }

function showExamSkip() {
  closeUnlockModal();
  alert("📝 امتحان التخطي:\nاختبر نفسك عبر الروابط الرسمية:\n• Cambridge: cambridgeenglish.org/test-your-english/\n• Goethe: goethe.de/en/spr/prf/ueb.html\n• DW: learngerman.dw.com");
}

function unlockNextLevel() {
  const idx = levelOrder.indexOf(currentLevel);
  if (idx < levelOrder.length-1) {
    currentLevel = levelOrder[idx+1];
    localStorage.setItem('currentLevel', currentLevel);
    updateLevelSelect();
    renderAll();
  }
  closeUnlockModal();
}

// ===== Reset Day =====
function resetDay() {
  if (!confirm('هل أنت متأكد إنك عايز تمسح تقدم اليوم؟')) return;
  const today = new Date().toDateString();
  Object.keys(completedTasks).forEach(k => { if (k.startsWith(today)) delete completedTasks[k]; });
  localStorage.setItem('completedTasks', JSON.stringify(completedTasks));

  if (lastStudyDate === today) {
    streak = Math.max(0, streak-1);
    lastStudyDate = '';
    localStorage.setItem('streak', streak);
    localStorage.setItem('lastStudyDate', lastStudyDate);
    document.getElementById('streakCount').textContent = streak;
    const ss = document.getElementById('streakCountSidebar');
    if (ss) ss.textContent = streak;
    if (levelStudyDays[currentLevel] > 0) { levelStudyDays[currentLevel]--; localStorage.setItem('levelStudyDays', JSON.stringify(levelStudyDays)); updateLevelProgress(); }
  }
  renderAll();
}

// ===== Render Lang Sections =====
function renderLangSections() {
  const today = new Date().toDateString(), dk = today+'_'+currentLevel;
  const dayIdx = new Date().getDay();
  const enContainer = document.getElementById('englishTasksContent');
  const deContainer = document.getElementById('germanTasksContent');
  if (!enContainer || !deContainer) return;

  const isFri = dayIdx === 5;
  const isThu = dayIdx === 4;

  if (isFri) {
    let enHtml = '', deHtml = '';
    fridayRest.sections.forEach((t, i) => {
      const html = `<div class="task-item" style="cursor:default;opacity:0.7"><div class="task-content"><div class="task-title">${t.title}</div><div class="task-desc">${t.desc}</div></div><div class="task-duration">${t.duration}</div></div>`;
      enHtml += html; deHtml += html;
    });
    enContainer.innerHTML = `<div class="tasks-section"><div class="section-title en">🇺🇸 English <span class="title-lang">يوم راحة</span></div>${enHtml}</div>`;
    deContainer.innerHTML = `<div class="tasks-section"><div class="section-title de">🇩🇪 Deutsch <span class="title-lang">يوم راحة</span></div>${deHtml}</div>`;
    return;
  }

  if (isThu) {
    let enHtml = '', deHtml = '';
    thursdayEnglish.sections.forEach((t, i) => {
      const id = today+'_thu_en_'+i;
      enHtml += taskHTML(id, t, completedTasks[id]||false);
    });
    thursdayGerman.sections.forEach((t, i) => {
      const id = today+'_thu_de_'+i;
      deHtml += taskHTML(id, t, completedTasks[id]||false);
    });
    enContainer.innerHTML = `<div class="tasks-section"><div class="section-title en">🇺🇸 English <span class="title-lang">${thursdayEnglish.sections.length} مهام</span></div>${enHtml}</div>`;
    deContainer.innerHTML = `<div class="tasks-section"><div class="section-title de">🇩🇪 Deutsch <span class="title-lang">${thursdayGerman.sections.length} مهام</span></div>${deHtml}</div>`;
    // Bind
    document.querySelectorAll('#englishTasksContent .task-item, #germanTasksContent .task-item').forEach(item => {
      const cb = function(e) { if (e.target.tagName === 'A' || e.target.closest('a')) return; toggleTask(this.dataset.taskId); };
      item.addEventListener('click', cb);
      item.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cb.call(this, e); } });
    });
    return;
  }

  const lv = levelData[currentLevel];
  if (!lv) return;

  let enHtml = '', deHtml = '';
  lv.en.forEach((t, i) => {
    const id = dk+'_en_'+i;
    enHtml += taskHTML(id, t, completedTasks[id]||false);
  });
  lv.de.forEach((t, i) => {
    const id = dk+'_de_'+i;
    deHtml += taskHTML(id, t, completedTasks[id]||false);
  });

  enContainer.innerHTML = `<div class="tasks-section"><div class="section-title en">🇺🇸 English <span class="title-lang" style="margin-right:0;margin-left:0;">${lv.en.length} مهام</span></div>${enHtml||'<p class="text-muted text-center" style="padding:20px;">لا توجد مهام</p>'}</div>`;
  deContainer.innerHTML = `<div class="tasks-section"><div class="section-title de">🇩🇪 Deutsch <span class="title-lang" style="margin-right:0;margin-left:0;">${lv.de.length} مهام</span></div>${deHtml||'<p class="text-muted text-center" style="padding:20px;">لا توجد مهام</p>'}</div>`;

  // Bind clicks for isolated sections
  document.querySelectorAll('#englishTasksContent .task-item, #germanTasksContent .task-item').forEach(item => {
    const cb = function(e) { if (e.target.tagName === 'A' || e.target.closest('a')) return; toggleTask(this.dataset.taskId); };
    item.addEventListener('click', cb);
    item.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cb.call(this, e); } });
  });
}

// ===== Timeline =====
function renderTimeline() {
  const grid = document.getElementById('timelineGrid');
  if (!grid) return;
  let html = '';
  levelOrder.forEach(level => {
    const cfg = levelConfig[level];
    const d = levelStudyDays[level] || 0;
    const pct = Math.min(100, Math.round((d/cfg.days)*100));
    const active = level === currentLevel ? 'active' : '';
    const completed = pct >= 100 ? 'completed' : '';
    html += `<div class="timeline-item ${active} ${completed}"><div class="tl-name">${cfg.emoji} ${cfg.name}</div><div class="tl-dur">${cfg.days} يوم</div><div class="tl-hours">${d}/${cfg.days} يوم (${pct}%)</div></div>`;
  });
  grid.innerHTML = html;
}

// ===== Vocab =====
async function saveVocab() {
  const input = document.getElementById('vocabInput');
  const word = input.value.trim();
  if (!word) { input.focus(); return; }
  const lang = document.getElementById('vocabLang').value;
  const type = document.getElementById('vocabType').value;
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrf) return;
  try {
    const resp = await fetch('save_vocab.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf_token: csrf, word, language: lang, type, study_date: new Date().toISOString().split('T')[0] })
    });
    const data = await resp.json();
    if (data.success) {
      input.value = '';
      input.focus();
      loadVocab();
    }
  } catch (e) { console.warn('Vocab save error:', e); }
}

async function loadVocab() {
  const container = document.getElementById('vocabList');
  if (!container) return;
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrf) return;
  try {
    const resp = await fetch('get_vocab.php?csrf_token=' + encodeURIComponent(csrf) + '&date=' + new Date().toISOString().split('T')[0]);
    const words = await resp.json();
    if (!words || !words.length) {
      container.innerHTML = '<div class="vocab-empty">لم تضف كلمات أو جمل اليوم بعد ✍️</div>';
      return;
    }
    let html = '<div style="display:flex;flex-wrap:wrap;gap:4px;">';
    words.forEach(w => {
      const tag = w.language === 'en' ? '🇺🇸' : '🇩🇪';
      const typeIcon = w.type === 'sentence' ? '💬' : '🔤';
      html += `<div class="vocab-tag ${w.language}"><span class="vocab-lang">${tag}</span> <span class="vocab-word">${escHtml(w.word)}</span> <span class="vocab-del" onclick="deleteVocab(${w.id})" title="حذف">✕</span></div>`;
    });
    html += '</div>';
    container.innerHTML = html;
  } catch (e) { console.warn('Vocab load error:', e); }
}

async function deleteVocab(id) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrf) return;
  try {
    await fetch('delete_vocab.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf_token: csrf, id })
    });
    loadVocab();
  } catch (e) {}
}

function escHtml(s) {
  const d = document.createElement('div');
  d.textContent = s;
  return d.innerHTML;
}

// ===== Journey =====
document.addEventListener('click', function(e) {
  const head = e.target.closest('.jlevel-head');
  if (head) {
    const level = head.closest('.jlevel');
    if (level) level.classList.toggle('open');
  }
});

// ===== Charts =====
let dailyChartInstance = null;
let weeklyChartInstance = null;
let levelChartInstance = null;
let langChartInstance = null;

async function renderCharts() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrf) return;

  try {
    const resp = await fetch('stats.php?type=overview&csrf_token=' + encodeURIComponent(csrf));
    const data = await resp.json();
    if (!data || !data.daily) return;

    // Summary stats
    const daily = data.daily;
    const totalDays = daily.length;
    const totalTasks = daily.reduce((s, d) => s + d.total, 0);
    document.getElementById('statTotalDays').textContent = totalDays;
    document.getElementById('statTotalTasks').textContent = totalTasks;
    document.getElementById('statBestStreak').textContent = data.streak || 0;
    document.getElementById('statCurrentLevel').textContent = (currentLevel || 'a1').toUpperCase();

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#a1a1aa' : '#475569';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const barColors = ['#14b8a6', '#fbbf24'];

    // Chart defaults
    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = gridColor;
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";

    // 1. Daily chart
    if (dailyChartInstance) dailyChartInstance.destroy();
    const ctx1 = document.getElementById('dailyChart')?.getContext('2d');
    if (ctx1) {
      const labels = daily.map(d => {
        const parts = d.date.split('-');
        return parts[1] + '/' + parts[2];
      });
      dailyChartInstance = new Chart(ctx1, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            { label: 'English 🇺🇸', data: daily.map(d => d.en_done), backgroundColor: '#14b8a6', borderRadius: 4 },
            { label: 'Deutsch 🇩🇪', data: daily.map(d => d.de_done), backgroundColor: '#fbbf24', borderRadius: 4 }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            x: { stacked: true, grid: { color: gridColor } },
            y: { stacked: true, beginAtZero: true, grid: { color: gridColor } }
          },
          plugins: { legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } } }
        }
      });
    }

    // 2. Weekly chart
    const weeklyResp = await fetch('stats.php?type=weekly&csrf_token=' + encodeURIComponent(csrf));
    const weeklyData = await weeklyResp.json();
    if (weeklyChartInstance) weeklyChartInstance.destroy();
    const ctx2 = document.getElementById('weeklyChart')?.getContext('2d');
    if (ctx2 && weeklyData.length) {
      const weekLabels = weeklyData.map((w, i) => 'أسبوع ' + (i + 1));
      weeklyChartInstance = new Chart(ctx2, {
        type: 'line',
        data: {
          labels: weekLabels,
          datasets: [
            { label: 'English', data: weeklyData.map(w => w.en_done), borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.1)', fill: true, tension: 0.3, pointRadius: 4 },
            { label: 'Deutsch', data: weeklyData.map(w => w.de_done), borderColor: '#fbbf24', backgroundColor: 'rgba(251,191,36,0.1)', fill: true, tension: 0.3, pointRadius: 4 }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: { y: { beginAtZero: true, grid: { color: gridColor } }, x: { grid: { color: gridColor } } },
          plugins: { legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } } }
        }
      });
    }

    // 3. Level distribution doughnut
    if (levelChartInstance) levelChartInstance.destroy();
    const ctx3 = document.getElementById('levelChart')?.getContext('2d');
    if (ctx3 && data.level_days) {
      const lv = data.level_days;
      const levelColors = ['#34d399', '#22d3ee', '#14b8a6', '#2dd4bf', '#f472b6'];
      levelChartInstance = new Chart(ctx3, {
        type: 'doughnut',
        data: {
          labels: ['A1', 'A2', 'B1', 'B2', 'C1'],
          datasets: [{ data: [lv.a1||0, lv.a2||0, lv.b1||0, lv.b2||0, lv.c1||0], backgroundColor: levelColors, borderWidth: 0 }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          cutout: '60%',
          plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } } }
        }
      });
    }

    // 4. Language comparison doughnut
    if (langChartInstance) langChartInstance.destroy();
    const ctx4 = document.getElementById('langChart')?.getContext('2d');
    if (ctx4) {
      const enTotal = daily.reduce((s, d) => s + d.en_done, 0);
      const deTotal = daily.reduce((s, d) => s + d.de_done, 0);
      langChartInstance = new Chart(ctx4, {
        type: 'doughnut',
        data: {
          labels: ['English 🇺🇸', 'Deutsch 🇩🇪'],
          datasets: [{ data: [enTotal, deTotal], backgroundColor: ['#14b8a6', '#fbbf24'], borderWidth: 0 }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          cutout: '60%',
          plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } } }
        }
      });
    }
  } catch (e) { console.warn('Charts error:', e); }
}

// Re-render charts when theme changes
const origToggle = toggleTheme;
toggleTheme = function() {
  origToggle();
  if (document.getElementById('section-stats').classList.contains('active')) {
    setTimeout(renderCharts, 300);
  }
};

// ===== Start =====
document.addEventListener('DOMContentLoaded', init);
