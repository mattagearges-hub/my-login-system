<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user_check = $stmt->fetch();

if (!$user_check || $user_check['is_active'] != 1) {
    $_SESSION['is_active'] = 0;
    header("Location: pending.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — تتبع اللغات</title>
    <meta name="csrf-token" content="<?= getCSRFToken() ?>">
    <link rel="stylesheet" href="style.css">
    <base target="_self">
</head>
<body>
    <a href="#main-content" class="skip-link">تخطى إلى المحتوى الرئيسي</a>

    <!-- Hamburger -->
    <button class="hamburger" id="hamburger" onclick="toggleSidebar()" aria-label="فتح القائمة">☰</button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="القائمة الرئيسية">
        <div class="sidebar-brand">
            <div class="logo-wrap">🎯</div>
            <h1>LinguaTrack</h1>
            <span class="sub">🇺🇸 B2 + 🇩🇪 C1</span>
        </div>

        <div class="sidebar-user">
            <div class="avatar"><?= mb_substr($_SESSION['username'] ?? 'U', 0, 1) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'مستخدم') ?></div>
                <div class="user-status">🔥 <span id="streakCountSidebar">0</span> يوم</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <button class="nav-item active" data-section="dashboard" onclick="switchSection('dashboard')">
                <span class="nav-icon">🏠</span> الرئيسية
            </button>
            <button class="nav-item" data-section="english" onclick="switchSection('english')">
                <span class="nav-icon">🇺🇸</span> English
            </button>
            <button class="nav-item" data-section="german" onclick="switchSection('german')">
                <span class="nav-icon">🇩🇪</span> Deutsch
            </button>
            <button class="nav-item" data-section="timeline" onclick="switchSection('timeline')">
                <span class="nav-icon">📈</span> الجدول الزمني
            </button>
            <button class="nav-item" data-section="stats" onclick="switchSection('stats'); renderCharts();">
                <span class="nav-icon">📊</span> إحصائيات
            </button>
            <button class="nav-item" data-section="journey" onclick="switchSection('journey')">
                <span class="nav-icon">🗺️</span> الرحلة
            </button>

            <div class="nav-divider"></div>

            <div class="theme-nav-item" onclick="toggleTheme()">
                <span>🌓 السمة</span>
                <div class="toggle-track" id="themeToggle">
                    <div class="toggle-thumb"></div>
                </div>
            </div>
        </nav>

        <div class="nav-footer">
            <a href="profile.php" class="nav-item">👤 الملف الشخصي</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="nav-item">🛡️ لوحة التحكم</a>
            <?php endif; ?>
            <a href="logout.php" class="nav-item" style="color: var(--danger);">🚪 تسجيل الخروج</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="main-content">

        <!-- ===== SECTION: Dashboard ===== -->
        <div class="page-section active" id="section-dashboard">

            <!-- Hero -->
            <div class="hero-header">
                <div class="greeting">👋 <?php $h = (int)date('H'); if ($h < 12) echo 'صباح الخير'; elseif ($h < 18) echo 'مساء الخير'; else echo 'مساء النور'; ?>, <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong></div>
                <h1>🎯 Lin<span style="color:var(--accent-2);">gua</span>Track — رحلة النهاردة</h1>
                <p class="subtitle" id="dailyQuote">كل يوم بتتقدم خطوة — والاستمرارية هي السر 💪</p>
                <div class="badges">
                    <span class="badge">🇺🇸 English: 2 ساعات</span>
                    <span class="badge">🇩🇪 Deutsch: 2 ساعات</span>
                    <span class="badge" id="levelBadge">📍 <span id="currentLevelNameSmall">A1</span></span>
                </div>
            </div>

            <!-- Daily Wisdom -->
            <div class="wisdom-bar">
                <span class="wisdom-icon">💡</span>
                <span class="wisdom-text" id="wisdomText">تعلّم لغة جديدة هو أن تصبح إنساناً آخر.. كل كلمة بتفتح لك عالم جديد</span>
            </div>

            <!-- Controls -->
            <div class="controls-row">
                <label for="levelSelect">📊 المستوى النشط:</label>
                <select id="levelSelect" aria-label="اختيار المستوى">
                    <option value="a1">🌱 A1 — الأساس</option>
                    <option value="a2">🌿 A2 — البناء</option>
                    <option value="b1">🌳 B1 — الاستقلال</option>
                    <option value="b2">🌲 B2 — الإتقان</option>
                    <option value="c1">🏔️ C1 — التخصص</option>
                </select>
                <span class="streak-badge">🔥 <span id="streakCount">0</span> أيام</span>
                <span style="color: var(--text-muted); font-size: 0.85em;">اليوم: <span id="currentDayDisplay" style="color: var(--accent-1); font-weight: 600;"></span></span>
            </div>

            <!-- Level Progress -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="font-size: 1em; font-weight: 600;">📊 <span id="currentLevelName">A1</span></h3>
                    <span style="font-size: 0.82em; color: var(--text-muted);"><span id="levelDaysStudied">0</span> / <span id="levelTotalDays">90</span> يوم</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress-label">
                        <span>نسبة الإنجاز</span>
                        <span id="levelPercentage">0%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" id="levelProgressBar" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

            <!-- Today's Progress -->
            <div class="card">
                <h3 style="font-size: 1em; font-weight: 600; margin-bottom: 16px;">📊 تقدم اليوم</h3>
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-number" id="completedTasks">0</div>
                        <div class="stat-label">مهام مكتملة</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalTasks">0</div>
                        <div class="stat-label">إجمالي المهام</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number green" id="studyHours">0</div>
                        <div class="stat-label">ساعات المذاكرة</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number green" id="streakCount2">0</div>
                        <div class="stat-label">أيام متتالية</div>
                    </div>
                </div>
                <div class="progress-wrap">
                    <div class="progress-label">
                        <span>تقدم اليوم</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" id="progressBar" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <button class="btn btn-danger-ghost btn-sm" style="margin-top:16px;" onclick="resetDay()">🔄 إعادة تعيين اليوم</button>
            </div>

            <!-- Vocab Input -->
            <div class="card" id="vocabCard">
                <h3 style="font-size:1em;font-weight:600;margin-bottom:8px;">📝 كلمات وجمل اليوم</h3>
                <p style="font-size:0.82em;color:var(--text-muted);margin-bottom:14px;">أضف الكلمات أو الجمل الجديدة اللي اتعلمتها النهاردة</p>
                <div class="vocab-input-row">
                    <input type="text" id="vocabInput" placeholder="اكتب الكلمة أو الجملة..." aria-label="كلمة جديدة" style="flex:1;">
                    <select id="vocabLang" aria-label="اللغة">
                        <option value="en">🇺🇸 English</option>
                        <option value="de">🇩🇪 Deutsch</option>
                    </select>
                    <select id="vocabType" aria-label="النوع">
                        <option value="word">كلمة</option>
                        <option value="sentence">جملة</option>
                    </select>
                    <button class="btn btn-primary btn-sm" onclick="saveVocab()">➕ إضافة</button>
                </div>
                <div id="vocabList" style="margin-top:14px;">
                    <!-- Generated by JS -->
                </div>
            </div>

            <!-- Day Content -->
            <div id="dayContent"></div>

        </div>

        <!-- ===== SECTION: English ===== -->
        <div class="page-section" id="section-english">
            <div class="page-header">
                <h2>🇺🇸 English Tasks</h2>
                <p>مهام اللغة الإنجليزية — المستوى <span id="enSectionLevel">A1</span></p>
            </div>
            <div id="englishTasksContent">
                <p class="text-muted text-center" style="padding: 40px;">اختر يوماً دراسياً لعرض المهام</p>
            </div>
            <div class="exam-section" style="margin-top:20px;">
                <h3>📝 اختبار تحديد المستوى — English</h3>
                <p style="font-size:0.88em;color:var(--text-secondary);margin-bottom:14px;">اختبر مستواك في الإنجليزية عبر الاختبارات الرسمية:</p>
                <a href="https://www.cambridgeenglish.org/test-your-english/" class="exam-link" >🧪 Cambridge English Test</a>
                <a href="https://www.efset.org/" class="exam-link" >📋 EF SET (50 min)</a>
            </div>
        </div>

        <!-- ===== SECTION: German ===== -->
        <div class="page-section" id="section-german">
            <div class="page-header">
                <h2>🇩🇪 Deutsch Aufgaben</h2>
                <p>مهام اللغة الألمانية — المستوى <span id="deSectionLevel">A1</span></p>
            </div>
            <div id="germanTasksContent">
                <p class="text-muted text-center" style="padding: 40px;">اختر يوماً دراسياً لعرض المهام</p>
            </div>
            <div class="exam-section" style="margin-top:20px;">
                <h3>📝 اختبار تحديد المستوى — Deutsch</h3>
                <p style="font-size:0.88em;color:var(--text-secondary);margin-bottom:14px;">اختبر مستواك في الألمانية عبر الاختبارات الرسمية:</p>
                <a href="https://www.goethe.de/en/spr/prf/ueb.html" class="exam-link" >🏛️ Goethe Test</a>
                <a href="https://learngerman.dw.com/ar/%D8%AA%D8%B9%D9%84%D9%8F%D9%91%D9%85-%D8%A7%D9%84%D8%A3%D9%84%D9%85%D8%A7%D9%86%D9%8A%D8%A9/s-9224" class="exam-link" >📺 DW Nicos Weg</a>
                <a href="https://www.onisetest.com/" class="exam-link" >📋 OnSet Test</a>
            </div>
        </div>

        <!-- ===== SECTION: Timeline ===== -->
        <div class="page-section" id="section-timeline">
            <div class="page-header">
                <h2>📈 الجدول الزمني للمستويات</h2>
                <p>نظرة عامة على كل المستويات ومدتها</p>
            </div>
            <div class="card">
                <div class="timeline-grid" id="timelineGrid">
                    <!-- Generated by JS -->
                </div>
            </div>
        </div>

        <!-- ===== SECTION: Statistics ===== -->
        <div class="page-section" id="section-stats">
            <div class="page-header">
                <h2>📊 إحصائيات التقدم</h2>
                <p>تحليل أدائك اليومي والأسبوعي</p>
            </div>

            <!-- Summary Cards -->
            <div class="stat-grid" id="statsSummary" style="margin-bottom:20px;">
                <div class="stat-card"><div class="stat-number" id="statTotalDays">0</div><div class="stat-label">أيام الدراسة</div></div>
                <div class="stat-card"><div class="stat-number" id="statTotalTasks">0</div><div class="stat-label">مهام مكتملة</div></div>
                <div class="stat-card"><div class="stat-number" id="statBestStreak">0</div><div class="stat-label">أفضل سلسلة</div></div>
                <div class="stat-card"><div class="stat-number" id="statCurrentLevel">-</div><div class="stat-label">المستوى الحالي</div></div>
            </div>

            <!-- Charts -->
            <div class="card">
                <h3 style="font-size:1em;font-weight:600;margin-bottom:16px;">📊 المهام اليومية (آخر 30 يوم)</h3>
                <div class="chart-container"><canvas id="dailyChart"></canvas></div>
            </div>

            <div class="card">
                <h3 style="font-size:1em;font-weight:600;margin-bottom:16px;">📈 التقدم الأسبوعي</h3>
                <div class="chart-container"><canvas id="weeklyChart"></canvas></div>
            </div>

            <div class="card charts-grid">
                <div>
                    <h3 style="font-size:1em;font-weight:600;margin-bottom:16px;">🍩 توزيع المستويات</h3>
                    <div class="chart-container" style="max-width:260px;margin:0 auto;"><canvas id="levelChart"></canvas></div>
                </div>
                <div>
                    <h3 style="font-size:1em;font-weight:600;margin-bottom:16px;">🇺🇸 English vs 🇩🇪 Deutsch</h3>
                    <div class="chart-container" style="max-width:260px;margin:0 auto;"><canvas id="langChart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- ===== SECTION: Journey ===== -->
        <div class="page-section" id="section-journey">

            <div class="hero-header" style="text-align:center;">
                <h1 style="font-size:2em;">🗺️ رحلتك نحو الطلاقة</h1>
                <p class="subtitle" style="font-size:1.05em;margin-bottom:0;">كل خطوة بتقربك من هدفك — والعلم نور 💡</p>
            </div>

            <!-- English Journey -->
            <div class="card">
                <h3 style="font-size:1.1em;font-weight:700;margin-bottom:12px;">🇺🇸 مسار الإنجليزية — الهدف: B2</h3>
                <p style="color:var(--text-secondary);font-size:0.88em;margin-bottom:16px;">رحلة من الصفر إلى الاحتراف في المحادثة والقراءة والكتابة</p>
                <div class="journey-levels">
                    <div class="jlevel" data-level="a1" style="--jl-color:#34d399;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#34d399;">🌱</span>
                            <span class="jlevel-name">A1 — مبتدئ</span>
                            <span class="jlevel-hours">180 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>👋 التحيات الأساسية — تقديم نفسك، الأرقام، الألوان، أيام الأسبوع</p>
                            <p>🔤 قراءة وكتابة الحروف — فهم كلمات وجمل بسيطة</p>
                            <p>🎯 بعد ما تخلص A1: تقدر تعرّف عن نفسك وتطلب حاجات بسيطة</p>
                            <div class="jlevel-motivate">💪 "أول خطوة هي أصعب خطوة — وأنت كسرت الحاجز!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="a2" style="--jl-color:#22d3ee;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#22d3ee;">🌿</span>
                            <span class="jlevel-name">A2 — ابتدائي</span>
                            <span class="jlevel-hours">240 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🛒 التسوق والطلب — التحدث عن الروتين اليومي والهوايات</p>
                            <p>📅 الماضي البسيط والمستقبل — تكوين جمل أطول</p>
                            <p>🎯 بعد ما تخلص A2: تقدر تسافر وتطلب وتتكلم عن نفسك بثقة</p>
                            <div class="jlevel-motivate">🔥 "أول 100 كلمة حفظتها.. أول جملة قولتها.. أنت بتتقدم بجد!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="b1" style="--jl-color:#14b8a6;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#14b8a6;">🌳</span>
                            <span class="jlevel-name">B1 — متوسط</span>
                            <span class="jlevel-hours">300 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🎬 فهم الأفلام والمسلسلات — مناقشة آرائك في مواضيع مألوفة</p>
                            <p>📖 قراءة مقالات قصيرة — كتابة إيميلات ورسائل</p>
                            <p>🎯 بعد ما تخلص B1: تقدر تعيش في بلد أجنبي وتتعامل يومياً</p>
                            <div class="jlevel-motivate">🌟 "B1 هو المفتاح — تقدر دلوقتي تتكلم عن رأيك وتفهم اللي حواليك!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="b2" style="--jl-color:#2dd4bf;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#2dd4bf;">🌲</span>
                            <span class="jlevel-name">B2 — فوق المتوسط 🎯</span>
                            <span class="jlevel-hours">360 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🎓 مناقشة مواضيع معقدة — التحدث بطلاقة مع الناطقين</p>
                            <p>📝 كتابة تقارير ومقالات — فهم النصوص الأكاديمية</p>
                            <p>💼 فرص عمل دولية — سفر بثقة — جامعة بالخارج</p>
                            <div class="jlevel-motivate">🏆 "B2 هو الهدف — تقدر تشتغل وتدرس وتتكلم إنجليزي زي لغة تانية!"</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- German Journey -->
            <div class="card">
                <h3 style="font-size:1.1em;font-weight:700;margin-bottom:12px;">🇩🇪 مسار الألمانية — الهدف: C1</h3>
                <p style="color:var(--text-secondary);font-size:0.88em;margin-bottom:16px;">رحلة من الصفر إلى الطلاقة الكاملة والتخصص</p>
                <div class="journey-levels">
                    <div class="jlevel" data-level="a1" style="--jl-color:#34d399;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#34d399;">🌱</span>
                            <span class="jlevel-name">A1 — مبتدئ</span>
                            <span class="jlevel-hours">180 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>👋 التحيات — الحروف والأصوات — der/die/das</p>
                            <p>🔤 تكوين جمل بسيطة — الأرقام والتاريخ</p>
                            <p>🎯 بعد A1: تقدر تعرّف عن نفسك وتسأل أسئلة بسيطة</p>
                            <div class="jlevel-motivate">💪 "Der Die Das أصعب حاجة في العالم — وبعد A1 هتبقى فاهمهم!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="a2" style="--jl-color:#22d3ee;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#22d3ee;">🌿</span>
                            <span class="jlevel-name">A2 — ابتدائي</span>
                            <span class="jlevel-hours">240 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🛒 التسوق والمواصلات — تصريف الأفعال في الماضي</p>
                            <p>📝 جمل أطول — حروف الجر — الحديث عن الروتين</p>
                            <p>🎯 بعد A2: تقدر تطلب أكل وتشتري حاجات وتتكلم عن يومك</p>
                            <div class="jlevel-motivate">🔥 "Das ist fantastisch! أول محادثة كاملة بالألماني!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="b1" style="--jl-color:#14b8a6;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#14b8a6;">🌳</span>
                            <span class="jlevel-name">B1 — متوسط</span>
                            <span class="jlevel-hours">300 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🎬 مشاهدة مسلسلات ألمانية (Dark, Berlin) بترجمة ألماني</p>
                            <p>📖 قراءة أخبار بسيطة — كتابة إيميلات رسمية</p>
                            <p>🎯 بعد B1: تقدر تقدم على فيزا لم الشمل أو Ausbildung</p>
                            <div class="jlevel-motivate">🌟 "B1 ألماني معناه إنك تقدر تعيش في ألمانيا وتتواصل!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="b2" style="--jl-color:#2dd4bf;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#2dd4bf;">🌲</span>
                            <span class="jlevel-name">B2 — فوق المتوسط</span>
                            <span class="jlevel-hours">360 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🎓 مناقشة مواضيع مجردة — فهم المحاضرات الجامعية</p>
                            <p>📝 كتابة مقالات مقنعة — نقاش مع ناطقين بطلاقة</p>
                            <p>🎯 بعد B2: تقدر تدرس في جامعة ألمانية أو تشتغل في ألمانيا</p>
                            <div class="jlevel-motivate">🏆 "B2 يخلّيك مرشح قوي لسوق العمل الألماني!"</div>
                        </div>
                    </div>
                    <div class="jlevel" data-level="c1" style="--jl-color:#f472b6;">
                        <div class="jlevel-head">
                            <span class="jlevel-badge" style="background:#f472b6;">🏔️</span>
                            <span class="jlevel-name">C1 — متقدم 🎯</span>
                            <span class="jlevel-hours">480 ساعة</span>
                        </div>
                        <div class="jlevel-body">
                            <p>🎤 التحدث بطلاقة تامة في أي موضوع — فهم الفروق الدقيقة</p>
                            <p>📚 قراءة الأدب الألماني — كتابة أبحاث وتقارير متخصصة</p>
                            <p>💼 الإقامة الدائمة — الجنسية — مناصب عليا — أكاديميا</p>
                            <div class="jlevel-motivate">👑 "C1 يعنيك مش بس بتتكلم ألماني — أنت بتفكر ألماني!"</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Big Picture -->
            <div class="card" style="background:var(--gradient-card);border-color:rgba(20,184,166,0.15);">
                <div style="text-align:center;">
                    <div style="font-size:3em;margin-bottom:12px;">🚀</div>
                    <h3 style="font-size:1.3em;font-weight:700;margin-bottom:8px;">الصورة الكاملة</h3>
                    <p style="color:var(--text-secondary);font-size:0.92em;margin-bottom:16px;">بعد ما توصل لـ B2 إنجليزي + C1 ألماني:</p>
                    <div class="big-picture-grid">
                        <div class="bp-item"><span style="font-size:2em;">💼</span><span>فرص عمل دولية — Companies like Google, Siemens, SAP</span></div>
                        <div class="bp-item"><span style="font-size:2em;">🎓</span><span>دراسة في الخارج — Master's في أمريكا أو ألمانيا</span></div>
                        <div class="bp-item"><span style="font-size:2em;">✈️</span><span>سافر أي مكان في العالم واتكلم مع الناس بطلاقة</span></div>
                        <div class="bp-item"><span style="font-size:2em;">🧠</span><span>دماغك هيبقى أسرع — تعلم لغتين بيحسن الذاكرة والتركيز</span></div>
                        <div class="bp-item"><span style="font-size:2em;">🌍</span><span>ثقافتين جديدتين — تفهم الأفلام، الكتب، الأخبار من المصدر</span></div>
                        <div class="bp-item"><span style="font-size:2em;">💰</span><span>راتب أعلى — ثنائي اللغة بيزود دخلك 10-20%</span></div>
                    </div>
                    <div style="margin-top:20px;padding:16px;background:rgba(20,184,166,0.06);border-radius:12px;border:1px solid rgba(20,184,166,0.1);">
                        <p style="font-size:1.05em;font-weight:600;color:var(--accent-1);">⏱️ إجمالي الرحلة: <span style="font-size:1.3em;">1,560 ساعة</span></p>
                        <p style="color:var(--text-secondary);font-size:0.88em;">تقريباً <strong>سنتين</strong> لو درست <strong>ساعتين</strong> كل يوم</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="page-footer">
            <p style="font-style: italic; font-size: 1.1em; color: var(--accent-1); margin-bottom: 8px;">
                "كل لغة عالم جديد.. وأنت بتبدأ رحلة هتخليك تعيش في عالمين إضافيين ✨"
            </p>
            <p>الاستمرارية تهزم الموهبة دايماً 💪</p>
        </div>
    </main>

    <!-- Celebration Modal -->
    <div class="modal-overlay" id="celebration" role="dialog" aria-modal="true" aria-labelledby="celebration-title">
        <div class="modal-box">
            <h2 id="celebration-title" style="background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">🎉 مبروك!</h2>
            <p>أنت خلصت كل مهام اليوم! استمر كده!</p>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="closeCelebration()">متابعة 🚀</button>
            </div>
        </div>
    </div>

    <!-- Unlock Modal -->
    <div class="modal-overlay" id="unlockModal" role="dialog" aria-modal="true" aria-labelledby="unlock-title">
        <div class="modal-box">
            <h2 id="unlock-title" style="color: var(--success);">🔓 فتح المستوى التالي</h2>
            <p>أنت خلصت <span id="unlockLevelProgress">0%</span> من المستوى الحالي!</p>
            <p style="font-size: 0.9em; color: var(--text-muted); margin-bottom: 20px;">
                عايز تمتحن امتحان تخطي ولا تنتظر لما تخلص المستوى؟
            </p>
            <div class="modal-actions">
                <button class="btn btn-success" onclick="unlockNextLevel()">🔓 فتح دلوقتي</button>
                <button class="btn btn-warning" onclick="showExamSkip()">📝 تخطي بامتحان</button>
                <button class="btn" style="background: var(--bg-card); color: var(--text-muted);" onclick="closeUnlockModal()">⏳ استنى</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="app.js"></script>
</body>
</html>
