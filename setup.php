<?php

require 'includes/db.php';

$db = get_db();


$db->exec("DROP TABLE IF EXISTS club_tags");
$db->exec("DROP TABLE IF EXISTS board_members");
$db->exec("DROP TABLE IF EXISTS events");
$db->exec("DROP TABLE IF EXISTS clubs");

// relevant club URLs
$db->exec("
    CREATE TABLE clubs (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        name          TEXT NOT NULL,
        description   TEXT,
        category      TEXT,
        logo_url      TEXT,
        slack_url     TEXT,
        discord_url   TEXT,
        instagram_url TEXT,
        featured      INTEGER DEFAULT 0
    )
");

$db->exec("
    CREATE TABLE events (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        club_id     INTEGER NOT NULL,
        day_of_week INTEGER NOT NULL,
        start_time  TEXT NOT NULL,
        end_time    TEXT NOT NULL,
        FOREIGN KEY (club_id) REFERENCES clubs(id)
    )
");

$db->exec("
    CREATE TABLE board_members (
        id               INTEGER PRIMARY KEY AUTOINCREMENT,
        club_id          INTEGER NOT NULL,
        name             TEXT NOT NULL,
        avatar_url       TEXT,
        discord_username TEXT,
        FOREIGN KEY (club_id) REFERENCES clubs(id)
    )
");

$db->exec("
    CREATE TABLE club_tags (
        id      INTEGER PRIMARY KEY AUTOINCREMENT,
        club_id INTEGER NOT NULL,
        tag     TEXT NOT NULL,
        FOREIGN KEY (club_id) REFERENCES clubs(id)
    )
");


$clubs = [

    /* Academic & Professional */
    ['Alliance of Latin Americans en Salud', 'Academic & Professional'],
    ['Black Pre-Health Student Association', 'Academic & Professional'],
    ['IMPACT', 'Academic & Professional'],
    ['Latine Pre-Law Sociedad', 'Academic & Professional'],
    ['Model United Nations', 'Academic & Professional'],
    ['Neuroscience Club', 'Academic & Professional'],
    ['Psych Club', 'Academic & Professional'],
    ['Public Health Club', 'Academic & Professional'],
    ['Rho Psi Eta', 'Academic & Professional'],
    ['Santa Clara University Mock Trial', 'Academic & Professional'],
    ['SCU History Club', 'Academic & Professional'],
    ['SCU Pre-Dental Society', 'Academic & Professional'],
    ['SCU Sociology Club', 'Academic & Professional'],
    ['Society of Physics Students', 'Academic & Professional'],
    ['The Student Chapter of the Association for Women in Mathematics', 'Academic & Professional'],
    ['Transfer Student Union', 'Academic & Professional'],

    /* Business */
    ['Alpha Kappa Psi', 'Business'],
    ['Black Business Association', 'Business'],
    ['Blockchain@SCU', 'Business'],
    ['Delta Sigma Pi: Co-ed Business Fraternity', 'Business'],
    ['Economic Student Association', 'Business'],
    ['Finance Technology Student Association', 'Business'],
    ['Information Systems and Analytics Student Network', 'Business'],
    ['International Business Club', 'Business'],
    ['Latinx Business Student Association', 'Business'],
    ['Microfinance Association', 'Business'],
    ['Phi Chi Theta', 'Business'],
    ['Queer & Qualified', 'Business'],
    ['Retail Studies Student Association', 'Business'],
    ['Santa Clara Consulting', 'Business'],
    ['Santa Clara Finance Association', 'Business'],
    ['Santa Clara Investment Banking Club', 'Business'],
    ['Santa Clara Investment Fund', 'Business'],
    ['Santa Clara University Accounting Association', 'Business'],
    ['Santa Clara University GetVirtual', 'Business'],
    ['Santa Clara University Institute for Operations Research', 'Business'],
    ['SCU Product Launch', 'Business'],
    ['SCU Sports Business', 'Business'],
    ['SCU Tech Sales Network', 'Business'],
    ['The Investment Club', 'Business'],
    ['The Women\'s Network', 'Business'],
    ['Undergraduate Marketing Association', 'Business'],
    ['Women in Business', 'Business'],

    /* CSO */
    ['APB', 'CSO'],
    ['ASG', 'CSO'],
    ['Into the Wild', 'CSO'],
    ['KSCU', 'CSO'],
    ['MCC', 'CSO'],
    ['SCCAP', 'CSO'],
    ['Santa Clara Review', 'CSO'],
    ['Santa Clara Review - The Owl', 'CSO'],
    ['The Redwood', 'CSO'],
    ['The Santa Clara Newspaper', 'CSO'],

    /* Cultural */
    ['African Student Association', 'Cultural'],
    ['Armenian Student Association', 'Cultural'],
    ['Asian Pacific-Islander Student Union', 'Cultural'],
    ['Barkada of SCU', 'Cultural'],
    ['Chinese Student Association', 'Cultural'],
    ['Cultural Italian American Organization', 'Cultural'],
    ['Eritrean-Ethiopian Student Association', 'Cultural'],
    ['Hermanas Unidas de SCU', 'Cultural'],
    ['Igwebuike', 'Cultural'],
    ['International Student Club', 'Cultural'],
    ['Japanese Student Association', 'Cultural'],
    ['Ka Mana\'o O Hawai\'i', 'Cultural'],
    ['Korean Student Association of Santa Clara University', 'Cultural'],
    ['Latiné Student Union', 'Cultural'],
    ['MEXT', 'Cultural'],
    ['Middle Eastern and North African Club', 'Cultural'],
    ['Native American Coalition for Change', 'Cultural'],
    ['QPOC Association', 'Cultural'],
    ['Russian Speaking Club', 'Cultural'],
    ['Santa Clara University Disabled Students Union', 'Cultural'],
    ['Taiwanese Student Association', 'Cultural'],
    ['Together for Ladies of Color', 'Cultural'],
    ['Vietnamese Student Association', 'Cultural'],

    /* Engineering */
    ['American Institute of Aeronautics and Astronautics SCU Chapter', 'Engineering'],
    ['American Society of Mechanical Engineers', 'Engineering'],
    ['Associated General Contractors', 'Engineering'],
    ['Association for Computing Machinery (ACM) - Women\'s Chapter', 'Engineering'],
    ['Association of Computing Machinery', 'Engineering'],
    ['Biomedical Engineering Society', 'Engineering'],
    ['DISC', 'Engineering'],
    ['Engineers Without Borders SCU', 'Engineering'],
    ['National Society of Black Engineers', 'Engineering'],
    ['Santa Clara Formula SAE', 'Engineering'],
    ['SCU Engineering Peer Advising', 'Engineering'],
    ['Society of Hispanic Professional Engineers', 'Engineering'],
    ['Society of Women Engineers', 'Engineering'],
    ['The Institute of Electrical and Electronics Engineers Student Chapter', 'Engineering'],
    ['The Maker Lab', 'Engineering'],
    ['Upsilon Epsilon Chapter of Theta Tau', 'Engineering'],
    ['Women in Cybersecurity', 'Engineering'],

    /* Faith-Based */
    ['Abide', 'Faith-Based'],
    ['Klesis Christian Fellowship', 'Faith-Based'],
    ['Muslim Student Association of Santa Clara University', 'Faith-Based'],
    ['New Life on Campus', 'Faith-Based'],
    ['Santa Clara University Young Life Club', 'Faith-Based'],
    ['SCU College Catholics', 'Faith-Based'],
    ['Sikh Student Association', 'Faith-Based'],

    /* Performance Arts */
    ['Ballet Folklórico de SCU', 'Performance Arts'],
    ['Daybreak KPOP Dance Crew', 'Performance Arts'],
    ['Santa Clara Ballroom Dance Association', 'Performance Arts'],
    ['SCU A Cappella', 'Performance Arts'],
    ['SCU Scriptless Improv', 'Performance Arts'],

    /* Recreational */
    ['Bronco Academy of Mentorship', 'Recreational'],
    ['Clara Craft Club', 'Recreational'],
    ['Santa Clara Pickleball Club', 'Recreational'],
    ['Santa Clara Rock Climbing Club', 'Recreational'],
    ['Santa Clara Run Club', 'Recreational'],
    ['Santa Clara Ski and Snowboard Club', 'Recreational'],
    ['Santa Clara University Student Art League', 'Recreational'],
    ['SCU Brazilian Jiu Jitsu', 'Recreational'],
    ['SCU Motorsports', 'Recreational'],
    ['SCU Surf Club', 'Recreational'],
    ['Smash @ SCU', 'Recreational'],
    ['Survivor: Santa Clara', 'Recreational'],

    /* Club Sports */
    ['Bowling', 'Club Sports'],
    ['Boxing', 'Club Sports'],
    ['Campus Recreation', 'Club Sports'],
    ['Club Golf', 'Club Sports'],
    ['Club Tennis', 'Club Sports'],
    ['Men\'s Club Baseball', 'Club Sports'],
    ['Men\'s Club Soccer', 'Club Sports'],
    ['Men\'s Ice Hockey', 'Club Sports'],
    ['Men\'s Lacrosse', 'Club Sports'],
    ['Men\'s Rugby Team', 'Club Sports'],
    ['Men\'s Ultimate', 'Club Sports'],
    ['Men\'s Volleyball', 'Club Sports'],
    ['Sailing', 'Club Sports'],
    ['Santa Clara Athletics - Ruff Riders', 'Club Sports'],
    ['Santa Clara University Bronco Pep Band', 'Club Sports'],
    ['Shotokan Karate', 'Club Sports'],
    ['Swimming', 'Club Sports'],
    ['Triathlon', 'Club Sports'],
    ['Women\'s Club Soccer', 'Club Sports'],
    ['Women\'s Club Volleyball', 'Club Sports'],
    ['Women\'s Field Hockey', 'Club Sports'],
    ['Women\'s Lacrosse', 'Club Sports'],
    ['Women\'s Rugby (BRUWS)', 'Club Sports'],
    ['Women\'s Ultimate', 'Club Sports'],

    /* Service & Social Justice */
    ['Active Minds at Santa Clara University', 'Service & Social Justice'],
    ['Alpha Phi Omega', 'Service & Social Justice'],
    ['More Than Your Body', 'Service & Social Justice'],
    ['Santa Clara MEDLIFE', 'Service & Social Justice'],
    ['Santa Clara University Belles Service Organization', 'Service & Social Justice'],
    ['SCU Global Medical Brigade', 'Service & Social Justice'],
    ['Sprout Up', 'Service & Social Justice'],
    ['Ethnic Studies Club', 'Service & Social Justice'],
    ['Future Child Advocates', 'Service & Social Justice'],
    ['Ignite National', 'Service & Social Justice'],
    ['Partners in Health Engage', 'Service & Social Justice'],
    ['SCU Broncos for Life', 'Service & Social Justice'],
    ['Students for Justice in Palestine', 'Service & Social Justice'],
    ['Surfrider SCU Chapter', 'Service & Social Justice'],
    ['Turning Point USA', 'Service & Social Justice'],
    ['tUrnout Climate Action', 'Service & Social Justice'],
    ['Undocumented Students & Allies Association', 'Service & Social Justice'],

    /* Special Interest */
    ['Game Night Club', 'Special Interest'],
    ['Hooks for Hope', 'Special Interest'],
    ['The American Sign Language Club', 'Special Interest'],
    ['Plant Futures at Santa Clara University', 'Special Interest'],
    ['Santa Clara Esports Club', 'Special Interest'],
    ['Santa Clara Fashion Club', 'Special Interest'],
    ['Santa Clara University Fungi Club', 'Special Interest'],
    ['Santa Clara University Television', 'Special Interest'],
    ['SCU Photography Club', 'Special Interest'],
    ['The Books and Tea Club', 'Special Interest'],
    ['Video Game Design Club', 'Special Interest'],
    ['Viniculture Santa Clara', 'Special Interest'],
];

$insert_club = $db->prepare("
    INSERT INTO clubs (name, category, featured) VALUES (?, ?, 0)
");

$insert_tag = $db->prepare("
    INSERT INTO club_tags (club_id, tag) VALUES (?, ?)
");

foreach ($clubs as $club_data) {
    $insert_club->execute([$club_data[0], $club_data[1]]);
    $club_id = $db->lastInsertId();
    $insert_tag->execute([$club_id, $club_data[1]]);
}


$featured_names = [
    'Association of Computing Machinery',
    'SCU A Cappella',
    'Santa Clara Run Club',
    'Model United Nations',
    'Santa Clara Esports Club',
    'Ballet Folklórico de SCU',
];

$set_featured = $db->prepare("UPDATE clubs SET featured = 1 WHERE name = ?");
foreach ($featured_names as $name) {
    $set_featured->execute([$name]);
}


$placeholder_events = [
    ['Association of Computing Machinery', 1, '18:00', '19:30'],
    ['Santa Clara Run Club', 2, '07:00', '08:00'],
    ['Model United Nations', 3, '17:00', '19:00'],
    ['SCU A Cappella', 4, '19:00', '20:30'],
    ['Santa Clara Esports Club', 5, '16:00', '18:00'],
    ['Ballet Folklórico de SCU', 2, '17:00', '18:30'],
    ['Psych Club', 1, '12:00', '13:00'],
    ['The Santa Clara Newspaper', 3, '13:00', '14:00'],
    ['SCU Photography Club', 4, '15:00', '16:30'],
    ['Game Night Club', 5, '18:00', '20:00'],
];

$insert_event = $db->prepare("
    INSERT INTO events (club_id, day_of_week, start_time, end_time)
    SELECT id, ?, ?, ? FROM clubs WHERE name = ?
");

foreach ($placeholder_events as $e) {
    $insert_event->execute([$e[1], $e[2], $e[3], $e[0]]);
}

$count = $db->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
echo "Done! $count clubs inserted across all categories.";
