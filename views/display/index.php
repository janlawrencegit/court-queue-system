<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php favicon_tags(); ?>
<title><?= e(app_name()) ?> — Live Display</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
:root {
    --bg: #0b1220;
    --surface: #151f32;
    --surface-2: #1c2940;
    --border: rgba(148, 163, 184, 0.12);
    --text: #f8fafc;
    --muted: #94a3b8;
    --vs: #fbbf24;
    --now-dim: rgba(52, 211, 153, 0.1);
    --next-dim: rgba(96, 165, 250, 0.08);
}

* { box-sizing: border-box; }

body {
    margin: 0;
    min-height: 100vh;
    height: 100vh;
    overflow: hidden;
    background: var(--bg);
    color: var(--text);
    font-family: "Segoe UI", system-ui, sans-serif;
    font-size: 14px;
    display: flex;
    flex-direction: column;
}

/* Prominent header — clock readable on TV */
.hdr {
    background: linear-gradient(120deg, #1d4ed8, #2563eb 50%, #0ea5e9);
    padding: clamp(0.75rem, 2vw, 1.25rem) clamp(1rem, 3vw, 2rem);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

.hdr-brand {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    min-width: 0;
}

.hdr-brand h1 {
    margin: 0;
    font-size: clamp(1.1rem, 2.2vw, 1.65rem);
    font-weight: 800;
    line-height: 1.15;
}

.hdr-brand .date {
    font-size: clamp(0.8rem, 1.4vw, 1rem);
    color: rgba(255, 255, 255, 0.88);
    font-weight: 500;
}

.clock {
    font-size: clamp(2rem, 6vw, 4rem);
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    line-height: 1;
    letter-spacing: 0.02em;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* Side-by-side: courts | waitlist */
.display-shell {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    flex: 1;
    min-height: 0;
    width: 100%;
}

.courts-pane {
    flex: 1 1 0;
    min-width: 0;
    min-height: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* 2-column court grid + auto carousel */
.courts-carousel {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    padding: clamp(0.5rem, 1vw, 0.85rem);
    --courts-gap: clamp(0.5rem, 1vw, 0.75rem);
}

.courts-viewport {
    flex: 1;
    min-height: 0;
    position: relative;
    overflow: hidden;
}

.courts-slides {
    height: 100%;
    position: relative;
}

.courts-slide {
    position: absolute;
    inset: 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    grid-template-rows: repeat(2, minmax(0, 1fr));
    gap: var(--courts-gap);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.5s ease, visibility 0.5s ease;
    pointer-events: none;
}

.courts-slide--rows-1 {
    grid-template-rows: minmax(0, 1fr);
}

.courts-slide--cols-1 {
    grid-template-columns: minmax(0, 1fr);
}

.courts-slide.is-active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    z-index: 1;
}

.courts-pager {
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.65rem;
    padding: 0.45rem 0 0.15rem;
    min-height: 1.75rem;
}

.courts-pager-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--muted);
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.courts-pager[hidden] {
    display: none;
}

.courts-pager-dot {
    width: 0.55rem;
    height: 0.55rem;
    padding: 0;
    border: none;
    border-radius: 50%;
    background: rgba(148, 163, 184, 0.35);
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
}

.courts-pager-dot.is-active {
    background: #60a5fa;
    transform: scale(1.25);
}

.courts-carousel--solo .courts-slide {
    grid-template-columns: minmax(0, 1fr);
    max-width: 100%;
    margin: 0;
    left: 0;
    right: 0;
}

/* Global waitlist sidebar — slightly narrower so courts get more room */
.waitlist-pane {
    flex: 0 0 clamp(210px, 18vw, 280px);
    width: clamp(210px, 18vw, 280px);
    max-width: 100%;
    background: var(--surface);
    border-left: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.waitlist-pane-hd {
    flex-shrink: 0;
    padding: 0.65rem 0.75rem;
    background: var(--surface-2);
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.waitlist-pane-title {
    margin: 0;
    font-size: clamp(0.85rem, 1.5vw, 1rem);
    font-weight: 800;
    color: #fcd34d;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.waitlist-pane-count {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--muted);
    white-space: nowrap;
}

.waitlist-pane-body {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0.45rem;
}

.waitlist-pane-empty {
    margin: 1rem 0.5rem;
    font-size: 0.8rem;
    color: #64748b;
    text-align: center;
    line-height: 1.45;
}

.global-wl {
    list-style: none;
    margin: 0;
    padding: 0;
}

.global-wl-item {
    display: flex;
    gap: 0.5rem;
    padding: 0.5rem 0.45rem;
    margin-bottom: 0.35rem;
    background: rgba(0, 0, 0, 0.22);
    border-radius: 8px;
    border: 1px solid rgba(148, 163, 184, 0.1);
}

.global-wl-item--next {
    border-color: rgba(96, 165, 250, 0.35);
    background: rgba(96, 165, 250, 0.08);
}

.global-wl-pos {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 2.25rem;
    padding-top: 0.1rem;
}

.global-wl-pos-num {
    font-size: 0.95rem;
    font-weight: 900;
    color: #fcd34d;
    line-height: 1.1;
}

.global-wl-item--next .global-wl-pos-num {
    color: #93c5fd;
}

.global-wl-pos-lbl {
    font-size: 0.5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #93c5fd;
    margin-top: 0.1rem;
}

.global-wl-content {
    flex: 1;
    min-width: 0;
}

.global-wl-court {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 0.35rem;
    margin-bottom: 0.2rem;
}

.global-wl-court-name {
    font-size: clamp(0.72rem, 1.2vw, 0.85rem);
    font-weight: 800;
    color: #e2e8f0;
    line-height: 1.2;
}

.global-wl-court-num {
    font-size: 0.58rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.global-wl-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-bottom: 0.25rem;
}

.global-wl-meta .tag {
    font-size: 0.52rem;
}

.global-wl-match {
    font-size: clamp(0.65rem, 1.1vw, 0.78rem);
    font-weight: 600;
    line-height: 1.25;
    color: #cbd5e1;
}

.global-wl-match .match-row {
    flex: 0 0 auto;
    min-height: 0;
    justify-content: flex-start;
}

.global-wl-match .match-row--singles {
    flex-direction: column;
    gap: 0.1rem;
    padding: 0;
    align-items: flex-start;
}

.global-wl-match .match-row--singles .match-player-line {
    align-items: flex-start;
    text-align: left;
    width: 100%;
}

.global-wl-match .match-row--singles .match-vs {
    align-self: flex-start;
    padding: 0.05rem 0;
}

.global-wl-match .match-row--teams {
    flex: 0 0 auto;
    justify-content: flex-start;
    gap: 0.35rem;
}

.global-wl-match .match-team {
    align-items: flex-start;
}

@media (max-width: 900px) {
    .display-shell {
        flex-direction: column;
    }
    .waitlist-pane {
        flex: 0 0 auto;
        width: 100%;
        border-left: none;
        border-top: 1px solid var(--border);
        max-height: min(42vh, 360px);
    }
}

.card-c {
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid var(--border);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 0;
    height: 100%;
}

/* Solo court — larger typography */
.courts-carousel--solo .hd {
    padding: 0.65rem 0.85rem;
}

.courts-carousel--solo .court-title {
    font-size: clamp(1.05rem, 2.5vw, 1.45rem);
    white-space: normal;
}

.courts-carousel--solo .court-meta {
    font-size: 0.72rem;
}

.courts-carousel--solo .court-status {
    font-size: 0.65rem;
    padding: 0.25rem 0.6rem;
}

.courts-carousel--solo .card-body-inner {
    padding: 0.65rem;
}

.courts-carousel--solo .queue-panels {
    gap: 0.5rem;
}

.courts-carousel--solo .qbox {
    padding: 0.55rem 0.45rem 0.65rem;
    min-height: 5rem;
}

.courts-carousel--solo .qbox .lbl {
    font-size: 0.72rem;
}

.courts-carousel--solo .qbox .match-row {
    min-height: 4rem;
}

.courts-carousel--solo .match-player-name {
    font-size: clamp(0.88rem, 2vw, 1.08rem);
}

.courts-carousel--solo .qbox--now .match-player-name {
    font-size: clamp(0.98rem, 2.2vw, 1.15rem);
}

.courts-carousel--solo .empty-slot {
    min-height: 3rem;
    font-size: 1.25rem;
}

.card-c .hd {
    padding: 0.4rem 0.55rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.35rem;
    background: var(--surface-2);
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}

.court-title-wrap { min-width: 0; flex: 1; }

.court-title {
    margin: 0;
    font-size: clamp(0.9rem, 1.6vw, 1.1rem);
    font-weight: 800;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.court-meta {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
    margin-top: 0.1rem;
    font-size: 0.6rem;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.court-waiting {
    color: #fcd34d;
    font-weight: 700;
}

.court-status {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.45rem;
    border-radius: 999px;
    font-size: 0.55rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    white-space: nowrap;
    border: 1px solid transparent;
    flex-shrink: 0;
}

.court-status::before {
    content: "";
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
}

.court-status--available { background: #064e3b; color: #6ee7b7; border-color: rgba(110, 231, 183, 0.3); }
.court-status--occupied { background: #78350f; color: #fde68a; border-color: rgba(253, 230, 138, 0.3); }
.court-status--closed { background: #7f1d1d; color: #fecaca; border-color: rgba(254, 202, 202, 0.3); }

.card-body-inner {
    padding: clamp(0.45rem, 0.8vw, 0.65rem);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.queue-panels {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(0.35rem, 0.8vw, 0.55rem);
    flex: 1;
    min-height: 0;
}

.qbox {
    border-radius: 8px;
    padding: clamp(0.28rem, 0.65vw, 0.45rem) clamp(0.25rem, 0.5vw, 0.38rem);
    min-height: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    text-align: center;
    border: 1px solid var(--border);
    background: rgba(0, 0, 0, 0.2);
}

.qbox--now { background: var(--now-dim); border-color: rgba(52, 211, 153, 0.2); }
.qbox--next { background: var(--next-dim); border-color: rgba(96, 165, 250, 0.15); }

.lbl {
    font-size: 0.55rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.qbox--now .lbl { color: #6ee7b7; }

.qbox-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.35rem;
    margin-bottom: 0.25rem;
    flex-shrink: 0;
}

.qbox-hd .lbl {
    margin-bottom: 0;
}

.play-duration {
    font-size: clamp(0.56rem, 1vw, 0.7rem);
    font-weight: 900;
    color: #fde68a;
    white-space: nowrap;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-variant-numeric: tabular-nums;
    min-width: 4.1rem;
    text-align: right;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.2rem;
    padding: 0.2rem 0.42rem;
    border-radius: 999px;
    border: 1px solid rgba(251, 191, 36, 0.4);
    background: linear-gradient(180deg, rgba(146, 64, 14, 0.5) 0%, rgba(120, 53, 15, 0.42) 100%);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.play-duration.play-duration--overdue {
    color: #fecaca;
    border-color: rgba(248, 113, 113, 0.5);
    background: linear-gradient(180deg, rgba(127, 29, 29, 0.6) 0%, rgba(153, 27, 27, 0.45) 100%);
    box-shadow: 0 1px 3px rgba(127, 29, 29, 0.45), inset 0 1px 0 rgba(254, 202, 202, 0.14);
}

.play-duration i {
    opacity: 0.95;
    margin-right: 0;
    font-size: 0.72em;
}
.qbox--next .lbl { color: #93c5fd; }

.slot-tags {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.2rem;
    margin-bottom: 0.25rem;
}

.tag {
    display: inline-block;
    padding: 0.1rem 0.35rem;
    border-radius: 4px;
    font-size: 0.55rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    line-height: 1.2;
}

.tag-qnum {
    font-family: ui-monospace, monospace;
    background: linear-gradient(180deg, rgba(30, 41, 59, 0.92) 0%, rgba(15, 23, 42, 0.95) 100%);
    color: #e2e8f0;
    text-transform: none;
    font-size: 0.6rem;
    font-weight: 900;
    letter-spacing: 0.03em;
    padding: 0.16rem 0.45rem;
    border: 1px solid rgba(148, 163, 184, 0.34);
    box-shadow: 0 1px 3px rgba(2, 6, 23, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.12);
}

.qbox--now .tag-qnum {
    color: #86efac;
    border-color: rgba(52, 211, 153, 0.42);
    background: linear-gradient(180deg, rgba(6, 78, 59, 0.92) 0%, rgba(5, 46, 22, 0.96) 100%);
}
.qbox--next .tag-qnum {
    color: #bfdbfe;
    border-color: rgba(96, 165, 250, 0.42);
    background: linear-gradient(180deg, rgba(30, 64, 175, 0.88) 0%, rgba(30, 58, 138, 0.95) 100%);
}

.qbox .match-row {
    display: flex;
    align-items: stretch;
    justify-content: flex-start;
    gap: 0.25rem;
    width: 100%;
    flex: 1;
    min-height: 0;
}

.match-row {
    display: flex;
    align-items: stretch;
    justify-content: flex-start;
    gap: 0.25rem;
    width: 100%;
}

/* Singles: stack names vertically — avoids crushed side-by-side text */
.match-row--singles {
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.1rem 0.15rem;
}

.match-row--singles .match-player-line {
    width: 100%;
    max-width: 100%;
}

.match-row--singles .match-vs {
    font-size: 0.65rem;
    padding: 0.05rem 0;
}

.match-row--teams {
    align-items: stretch;
}

.match-team {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: flex-start;
    gap: 0.25rem;
    flex: 1;
    min-width: 0;
    padding: 0 0.08rem;
}

.match-player-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.4rem;
    width: 100%;
    min-width: 0;
    text-align: left;
    background: rgba(15, 23, 42, 0.42);
    border: 1px solid rgba(148, 163, 184, 0.22);
    border-radius: 8px;
    padding: 0.27rem 0.4rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
}

.match-player-name {
    font-size: clamp(0.64rem, 1.15vw, 0.82rem);
    font-weight: 700;
    line-height: 1.2;
    color: #e2e8f0;
    word-break: normal;
    overflow-wrap: anywhere;
    max-width: 100%;
}

.qbox--now .match-player-name {
    font-weight: 800;
    color: #ecfdf5;
    font-size: clamp(0.66rem, 1.2vw, 0.85rem);
}

.qbox--now .match-player-line {
    background: rgba(6, 78, 59, 0.38);
    border-color: rgba(110, 231, 183, 0.24);
}

.qbox--next .match-player-line {
    background: rgba(30, 64, 175, 0.18);
    border-color: rgba(147, 197, 253, 0.2);
}

.match-player-meta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.player-skill {
    display: inline-block;
    font-size: clamp(0.5rem, 0.95vw, 0.58rem);
    font-weight: 900;
    letter-spacing: 0.06em;
    line-height: 1;
    padding: 0.18em 0.4em;
    border-radius: 4px;
    border: 1px solid transparent;
}

.player-skill.skill--beginner {
    color: #94a3b8;
    background: rgba(148, 163, 184, 0.12);
    border-color: rgba(148, 163, 184, 0.25);
}

.player-skill.skill--intermediate {
    color: #60a5fa;
    background: rgba(96, 165, 250, 0.12);
    border-color: rgba(96, 165, 250, 0.28);
}

.player-skill.skill--advanced {
    color: #fbbf24;
    background: rgba(251, 191, 36, 0.12);
    border-color: rgba(251, 191, 36, 0.28);
}

.player-skill.skill--pro {
    color: #f87171;
    background: rgba(239, 68, 68, 0.18);
    border-color: rgba(248, 113, 113, 0.45);
}

.player-times {
    font-size: clamp(0.5rem, 0.95vw, 0.58rem);
    font-weight: 800;
    color: #94a3b8;
    letter-spacing: 0.02em;
}

.global-wl-match .match-player-name {
    font-size: clamp(0.65rem, 1.1vw, 0.78rem);
}

.global-wl-match .player-skill,
.global-wl-match .player-times {
    font-size: clamp(0.48rem, 0.9vw, 0.55rem);
}

.match-vs {
    font-size: 0.6rem;
    font-weight: 900;
    color: var(--vs);
    flex-shrink: 0;
    padding: 0 0.1rem;
    align-self: center;
    display: none;
}

.empty-slot {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    font-size: 1rem;
    min-height: 2.5rem;
}

/* Very narrow: stack Now / Next vertically inside card */
@media (max-width: 360px) {
    .queue-panels { grid-template-columns: 1fr; }
}

/* Ultra-wide TV: slightly larger text, still dense */
@media (min-width: 1920px) {
    .match-player-name { font-size: 0.9rem; }
    .qbox--now .match-player-name { font-size: 0.95rem; }
}
</style>
</head>
<body>
<header class="hdr">
    <div class="hdr-brand">
        <h1><i class="fa-solid fa-table-tennis-paddle-ball me-1"></i><?= e(app_name()) ?></h1>
        <span class="date" id="date"></span>
    </div>
    <div class="clock" id="clock" aria-live="polite"></div>
</header>

<?php
/** @var list<array<string, mixed>> $courts */
$courtCount = count($courts);
$globalWaitlist = $globalWaitlist ?? [];
?>
<div class="display-shell">
<section class="courts-pane" aria-label="Courts">
<div class="courts-carousel<?= $courtCount <= 1 ? ' courts-carousel--solo' : '' ?>" id="courts-carousel" data-court-count="<?= (int) $courtCount ?>">
    <div class="courts-viewport">
        <div class="courts-slides" id="courts-slides">
<?php foreach ($courts as $c):
    $status = $c['status'] ?? 'available';
    $statusClass = in_array($status, ['available', 'occupied', 'closed'], true) ? $status : 'available';
    $waiting = (int) ($c['waiting_count'] ?? 0);
?>
<article class="card-c" data-court-id="<?= (int) $c['id'] ?>">
    <header class="hd">
        <div class="court-title-wrap">
            <h2 class="court-title" title="<?= e($c['court_number']) ?>"><?= e($c['court_number']) ?></h2>
            <div class="court-meta">
                <?php if ($waiting > 0): ?>
                    <span class="court-waiting"><i class="fas fa-hourglass-half"></i> <?= $waiting ?> in queue</span>
                <?php endif; ?>
            </div>
        </div>
        <span class="court-status court-status--<?= e($statusClass) ?>"><?= e(ucfirst($status)) ?></span>
    </header>
    <div class="card-body-inner">
        <div class="queue-panels">
            <div class="qbox qbox--now">
                <div class="qbox-hd">
                    <div class="lbl">Now</div>
                    <?php if (!empty($c['current_queue']['rental_ends_at'])): ?>
                    <span class="play-duration" data-rental-ends-at="<?= e($c['current_queue']['rental_ends_at']) ?>">
                        <i class="fas fa-stopwatch"></i>—
                    </span>
                    <?php endif; ?>
                </div>
                <?php $queue = $c['current_queue'] ?? null; require BASE_PATH . '/views/partials/display_queue_slot.php'; ?>
            </div>
            <div class="qbox qbox--next">
                <div class="lbl">Next</div>
                <?php $queue = $c['next_queue'] ?? null; require BASE_PATH . '/views/partials/display_queue_slot.php'; ?>
            </div>
</div>
</div>
</article>
<?php endforeach; ?>
        </div>
    </div>
    <nav class="courts-pager" id="courts-pager" aria-label="Court pages" hidden></nav>
</div>
</section>
<?php require BASE_PATH . '/views/partials/display_global_waitlist.php'; ?>
</div>
<script>
const DISPLAY_DATA_URL = <?= json_encode(url('display/data')) ?>;
const DISPLAY_REFRESH_MS = <?= max(5, (int) (setting('display_refresh_interval', '10') ?? 10)) * 1000 ?>;

function tick() {
    const n = new Date();
    document.getElementById('clock').textContent = n.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', second: '2-digit' });
    document.getElementById('date').textContent = n.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    if (typeof window.tickPlayDurations === 'function') {
        window.tickPlayDurations();
    }
}
tick();
setInterval(tick, 1000);

(function () {
    function esc(value) {
        const el = document.createElement('span');
        el.textContent = value == null ? '' : String(value);
        return el.innerHTML;
    }

    function formatRentalTimer(secondsLeft) {
        if (secondsLeft == null) {
            return '—';
        }
        const abs = Math.abs(secondsLeft);
        const h = Math.floor(abs / 3600);
        const m = Math.floor((abs % 3600) / 60);
        const s = abs % 60;
        let base = '';
        if (h > 0) {
            base = h + 'h ' + m + 'm ' + s + 's';
        } else if (m > 0) {
            base = m + 'm ' + s + 's';
        } else {
            base = s + 's';
        }
        return secondsLeft < 0 ? '-' + base : base;
    }

    function secondsUntilIso(iso) {
        if (!iso) {
            return null;
        }
        const endAt = new Date(String(iso).replace(' ', 'T'));
        if (isNaN(endAt.getTime())) {
            return null;
        }
        return Math.floor((endAt.getTime() - Date.now()) / 1000);
    }

    function renderNowHeader(queue) {
        let html = '<div class="qbox-hd"><div class="lbl">Now</div>';
        if (queue && queue.rental_ends_at) {
            const secs = secondsUntilIso(queue.rental_ends_at);
            const overdueClass = secs < 0 ? ' play-duration--overdue' : '';
            html += '<span class="play-duration' + overdueClass + '" data-rental-ends-at="' + esc(queue.rental_ends_at) + '">';
            html += '<i class="fas fa-stopwatch"></i>' + esc(formatRentalTimer(secs));
            html += '</span>';
        }
        html += '</div>';
        return html;
    }

    function tickPlayDurations() {
        document.querySelectorAll('.play-duration[data-rental-ends-at]').forEach(function (el) {
            const secs = secondsUntilIso(el.dataset.rentalEndsAt);
            if (secs == null) {
                return;
            }
            el.classList.toggle('play-duration--overdue', secs < 0);
            el.innerHTML = '<i class="fas fa-stopwatch"></i>' + esc(formatRentalTimer(secs));
        });
    }

    function teamCards(queue, teamNum) {
        const key = teamNum === 2 ? 'display_team2_cards' : 'display_team1_cards';
        if (queue && Array.isArray(queue[key]) && queue[key].length) {
            return queue[key];
        }
        const namesKey = teamNum === 2 ? 'display_team2' : 'display_team1';
        const names = queue && Array.isArray(queue[namesKey]) ? queue[namesKey].filter(Boolean) : [];
        return names.map(function (name) {
            return { name: name, skill_code: 'INT', skill_class: 'intermediate', games_played: 0 };
        });
    }

    function renderPlayerLine(player) {
        const skillCode = player.skill_code || 'INT';
        const skillClass = player.skill_class || 'intermediate';
        const games = player.games_played != null ? player.games_played : 0;
        let html = '<div class="match-player-line">';
        html += '<span class="match-player-name">' + esc(player.name) + '</span>';
        html += '<span class="match-player-meta">';
        html += '<span class="player-skill skill--' + esc(skillClass) + '">' + esc(skillCode) + '</span>';
        html += '<span class="player-times">' + games + '×</span>';
        html += '</span></div>';
        return html;
    }

    function renderMatchRow(queue, hideTags) {
        if (!queue) {
            return '<div class="empty-slot" aria-hidden="true">—</div>';
        }
        const players = teamCards(queue, 1).concat(teamCards(queue, 2));
        if (!players.length) {
            return '<div class="empty-slot" aria-hidden="true">—</div>';
        }

        let html = '';

        if (!hideTags) {
            html += '<div class="slot-tags">';
            html += '<span class="tag tag-qnum">' + esc(queue.queue_number) + '</span>';
            html += '</div>';
        }

        html += '<div class="match-row match-row--teams">';
        html += '<div class="match-team">';
        players.forEach(function (player) {
            html += renderPlayerLine(player);
        });
        html += '</div></div>';
        return html;
    }

    function renderWaitlistMatch(queue) {
        if (!queue) {
            return '';
        }
        return '<div class="global-wl-match">' + renderMatchRow(queue, true) + '</div>';
    }

    function updateCourtCard(card, court) {
        const status = ['available', 'occupied', 'closed'].includes(court.status) ? court.status : 'available';
        const statusEl = card.querySelector('.court-status');
        statusEl.className = 'court-status court-status--' + status;
        statusEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);

        const titleEl = card.querySelector('.court-title');
        titleEl.textContent = court.court_number;
        titleEl.title = court.court_number;

        const metaEl = card.querySelector('.court-meta');
        let metaHtml = '';
        if ((court.waiting_count || 0) > 0) {
            metaHtml += '<span class="court-waiting"><i class="fas fa-hourglass-half"></i> ' + court.waiting_count + ' in queue</span>';
        }
        metaEl.innerHTML = metaHtml;

        card.querySelector('.qbox--now').innerHTML = renderNowHeader(court.current_queue) + renderMatchRow(court.current_queue);
        card.querySelector('.qbox--next').innerHTML = '<div class="lbl">Next</div>' + renderMatchRow(court.next_queue);
    }

    function buildCourtCard(court) {
        const status = ['available', 'occupied', 'closed'].includes(court.status) ? court.status : 'available';
        let metaHtml = '';
        if ((court.waiting_count || 0) > 0) {
            metaHtml += '<span class="court-waiting"><i class="fas fa-hourglass-half"></i> ' + court.waiting_count + ' in queue</span>';
        }

        const card = document.createElement('article');
        card.className = 'card-c';
        card.dataset.courtId = String(court.id);
        card.innerHTML =
            '<header class="hd">' +
                '<div class="court-title-wrap">' +
                    '<h2 class="court-title" title="' + esc(court.court_number) + '">' + esc(court.court_number) + '</h2>' +
                    '<div class="court-meta">' + metaHtml + '</div>' +
                '</div>' +
                '<span class="court-status court-status--' + status + '">' + esc(status.charAt(0).toUpperCase() + status.slice(1)) + '</span>' +
            '</header>' +
            '<div class="card-body-inner">' +
                '<div class="queue-panels">' +
                    '<div class="qbox qbox--now">' + renderNowHeader(court.current_queue) + renderMatchRow(court.current_queue) + '</div>' +
                    '<div class="qbox qbox--next"><div class="lbl">Next</div>' + renderMatchRow(court.next_queue) + '</div>' +
                '</div>' +
            '</div>';
        return card;
    }

    function renderWaitlist(waitlist) {
        const body = document.getElementById('waitlist-body');
        const countEl = document.getElementById('waitlist-count');
        if (!body || !countEl) {
            return;
        }

        countEl.textContent = waitlist.length + ' waiting';
        if (!waitlist.length) {
            body.innerHTML = '<p class="waitlist-pane-empty">No matches waiting. Check the courts for live play.</p>';
            return;
        }

        let html = '<ul class="global-wl">';
        waitlist.forEach(function (item) {
            html += '<li class="global-wl-item' + (item.is_next ? ' global-wl-item--next' : '') + '">';
            html += '<div class="global-wl-pos" title="Position on this court">';
            html += '<span class="global-wl-pos-num">#' + item.position + '</span>';
            if (item.is_next) {
                html += '<span class="global-wl-pos-lbl">Next</span>';
            }
            html += '</div><div class="global-wl-content">';
            html += '<div class="global-wl-court">';
            html += '<span class="global-wl-court-name">' + esc(item.court_number) + '</span>';
            html += '</div><div class="global-wl-meta">';
            html += '<span class="tag tag-qnum">' + esc(item.queue_number) + '</span>';
            html += '</div>';
            html += renderWaitlistMatch(item);
            html += '</div></li>';
        });
        html += '</ul>';
        body.innerHTML = html;
    }

    const SLIDE_MS = 7000;
    const MAX_ROWS = 2;
    const carousel = document.getElementById('courts-carousel');
    const slidesHost = document.getElementById('courts-slides');
    const pager = document.getElementById('courts-pager');
    if (!carousel || !slidesHost || !pager) {
        return;
    }

    let cards = [];
    let currentPage = 0;
    let pageCount = 0;
    let timer = null;
    let refreshBusy = false;

    function columnCount() {
        return carousel.classList.contains('courts-carousel--solo') ? 1 : 2;
    }

    function getCourtsPerPage(totalCards) {
        const cols = columnCount();
        const maxPerPage = cols * MAX_ROWS;
        if (totalCards <= maxPerPage) {
            return totalCards;
        }
        return maxPerPage;
    }

    function slideLayoutClass(count) {
        const cols = columnCount();
        const rows = Math.max(1, Math.ceil(count / cols));
        const classes = [];
        if (rows === 1) {
            classes.push('courts-slide--rows-1');
        }
        if (cols === 1) {
            classes.push('courts-slide--cols-1');
        }
        return classes.join(' ');
    }

    function goTo(page) {
        if (pageCount <= 1) {
            return;
        }
        currentPage = ((page % pageCount) + pageCount) % pageCount;
        slidesHost.querySelectorAll('.courts-slide').forEach(function (slide, i) {
            slide.classList.toggle('is-active', i === currentPage);
        });
        pager.querySelectorAll('.courts-pager-dot').forEach(function (dot, i) {
            dot.classList.toggle('is-active', i === currentPage);
            dot.setAttribute('aria-current', i === currentPage ? 'true' : 'false');
        });
        const label = pager.querySelector('.courts-pager-label');
        if (label) {
            label.textContent = (currentPage + 1) + ' / ' + pageCount;
        }
    }

    function renderPager() {
        pager.innerHTML = '';
        if (pageCount <= 1) {
            pager.hidden = true;
            return;
        }
        pager.hidden = false;

        const label = document.createElement('span');
        label.className = 'courts-pager-label';
        label.textContent = (currentPage + 1) + ' / ' + pageCount;
        pager.appendChild(label);

        for (let i = 0; i < pageCount; i++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'courts-pager-dot' + (i === currentPage ? ' is-active' : '');
            dot.setAttribute('aria-label', 'Court page ' + (i + 1) + ' of ' + pageCount);
            dot.setAttribute('aria-current', i === currentPage ? 'true' : 'false');
            dot.addEventListener('click', function () {
                goTo(i);
                restartTimer();
            });
            pager.appendChild(dot);
        }
    }

    function buildSlides(preservePage) {
        if (!cards.length) {
            cards = Array.prototype.slice.call(document.querySelectorAll('.card-c[data-court-id]'));
        }
        const savedPage = preservePage ? currentPage : 0;
        const perPage = Math.max(1, getCourtsPerPage(cards.length));
        slidesHost.innerHTML = '';
        pageCount = Math.max(1, Math.ceil(cards.length / perPage));
        currentPage = Math.min(savedPage, pageCount - 1);

        for (let i = 0; i < pageCount; i++) {
            const slice = cards.slice(i * perPage, (i + 1) * perPage);
            const slide = document.createElement('div');
            slide.className = 'courts-slide ' + slideLayoutClass(slice.length) + (i === currentPage ? ' is-active' : '');
            slide.setAttribute('role', 'group');
            slide.setAttribute('aria-label', 'Courts page ' + (i + 1));
            slice.forEach(function (card) {
                slide.appendChild(card);
            });
            slidesHost.appendChild(slide);
        }

        renderPager();
        restartTimer();
    }

    function nextPage() {
        goTo(currentPage + 1);
    }

    function restartTimer() {
        clearInterval(timer);
        if (pageCount > 1) {
            timer = setInterval(nextPage, SLIDE_MS);
        }
    }

    function syncCourtsFromData(courts) {
        const existing = Array.prototype.slice.call(document.querySelectorAll('.card-c[data-court-id]'));
        const existingIds = existing.map(function (card) {
            return card.dataset.courtId;
        }).sort().join(',');

        const incomingIds = courts.map(function (court) {
            return String(court.id);
        }).sort().join(',');

        carousel.classList.toggle('courts-carousel--solo', courts.length <= 1);
        carousel.dataset.courtCount = String(courts.length);

        if (existingIds !== incomingIds) {
            const fragment = document.createDocumentFragment();
            courts.forEach(function (court) {
                fragment.appendChild(buildCourtCard(court));
            });
            slidesHost.innerHTML = '';
            slidesHost.appendChild(fragment);
            cards = Array.prototype.slice.call(slidesHost.querySelectorAll('.card-c[data-court-id]'));
            buildSlides(true);
            return;
        }

        courts.forEach(function (court) {
            const card = document.querySelector('.card-c[data-court-id="' + court.id + '"]');
            if (card) {
                updateCourtCard(card, court);
            }
        });
    }

    function silentRefresh() {
        if (refreshBusy) {
            return;
        }
        refreshBusy = true;
        fetch(DISPLAY_DATA_URL, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store'
        })
            .then(function (res) {
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                return res.json();
            })
            .then(function (payload) {
                if (!payload || !payload.success) {
                    return;
                }
                syncCourtsFromData(payload.courts || []);
                renderWaitlist(payload.waitlist || []);
                tickPlayDurations();
            })
            .catch(function () {
                /* silent — retry on next interval */
            })
            .finally(function () {
                refreshBusy = false;
            });
    }

    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            buildSlides(true);
        }, 250);
    });

    buildSlides(false);
    setInterval(silentRefresh, DISPLAY_REFRESH_MS);
    window.tickPlayDurations = tickPlayDurations;
    tickPlayDurations();
})();
</script>
</body>
</html>
