<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grid Battle Simulator</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #1a1a2e;
        color: #fff;
        font-family: 'Segoe UI', Arial, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    h1 {
        font-size: 2rem;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #e94560, #0f3460);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    #controls {
        margin: 15px 0;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    button {
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    #startBtn {
        background: linear-gradient(135deg, #e94560, #c23152);
        color: #fff;
    }

    #resetBtn {
        background: linear-gradient(135deg, #0f3460, #16213e);
        color: #fff;
    }

    button:hover {
        transform: scale(1.05);
        box-shadow: 0 0 15px rgba(233, 69, 96, 0.5);
    }

    #speedControl {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #aaa;
    }

    #speedControl input {
        width: 100px;
    }

    #battleInfo {
        display: flex;
        justify-content: space-between;
        width: 620px;
        margin-bottom: 10px;
    }

    .team-info {
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: bold;
        min-width: 180px;
        text-align: center;
    }

    .team1-info {
        background: rgba(52, 152, 219, 0.3);
        border: 1px solid #3498db;
    }

    .team2-info {
        background: rgba(231, 76, 60, 0.3);
        border: 1px solid #e74c3c;
    }

    #statusBar {
        background: rgba(255,255,255,0.1);
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #f1c40f;
        min-height: 35px;
        display: flex;
        align-items: center;
    }

    #grid {
        display: grid;
        grid-template-columns: repeat(10, 60px);
        grid-template-rows: repeat(10, 60px);
        gap: 2px;
        background: #16213e;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
        margin: 10px 0;
    }

    .cell {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        position: relative;
        transition: background 0.3s;
    }

    .cell-neutral {
        background: #1a1a2e;
    }

    .cell-team1-zone {
        background: rgba(52, 152, 219, 0.08);
    }

    .cell-team2-zone {
        background: rgba(231, 76, 60, 0.08);
    }

    .troop-icon {
        font-size: 1.5rem;
        line-height: 1;
    }

    .troop-name {
        font-size: 0.5rem;
        margin-top: 1px;
        opacity: 0.9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 55px;
        text-align: center;
    }

    .hp-bar-bg {
        position: absolute;
        bottom: 2px;
        left: 4px;
        right: 4px;
        height: 4px;
        background: rgba(0,0,0,0.5);
        border-radius: 2px;
    }

    .hp-bar {
        height: 100%;
        border-radius: 2px;
        transition: width 0.2s;
    }

    .hp-bar-team1 {
        background: #3498db;
    }

    .hp-bar-team2 {
        background: #e74c3c;
    }

    .team1-troop .troop-name { color: #85c1e9; }
    .team2-troop .troop-name { color: #f1948a; }

    .cell-attack {
        animation: attackFlash 0.3s;
        background: rgba(255, 0, 0, 0.25); /* base red glow */
    }

    @keyframes attackFlash {
        0%, 100% { box-shadow: none; background: rgba(255, 0, 0, 0.15); }
        50% { box-shadow: inset 0 0 20px rgba(255, 0, 0, 0.8); background: rgba(255, 0, 0, 0.35); }
    }

    .cell-damaged {
        animation: damagedFlash 0.4s;
    }

    @keyframes damagedFlash {
        0%, 100% { background: transparent; }
        50% { background: rgba(255, 0, 0, 0.55); }
    }

    .cell-death {
        animation: deathFlash 0.5s;
    }

    @keyframes deathFlash {
        0% { background: rgba(255,0,0,0.5); }
        100% { background: transparent; }
    }

    #log {
        width: 620px;
        max-height: 200px;
        overflow-y: auto;
        background: #16213e;
        border-radius: 10px;
        padding: 10px;
        margin-top: 10px;
        font-size: 0.75rem;
        color: #aaa;
    }

    #log div {
        padding: 2px 5px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .log-attack { color: #f39c12; }
    .log-kill { color: #e74c3c; font-weight: bold; }
    .log-move { color: #7f8c8d; }
    .log-win { color: #2ecc71; font-weight: bold; font-size: 1rem; }

    #troopSidebar {
        width: 620px;
        margin-top: 15px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .troop-card {
        background: #16213e;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.7rem;
        min-width: 120px;
        border-left: 3px solid #555;
    }

    .troop-card.team1 { border-left-color: #3498db; }
    .troop-card.team2 { border-left-color: #e74c3c; }
    .troop-card .card-name { font-weight: bold; font-size: 0.85rem; }
    .troop-card .card-stat { color: #aaa; }
</style>
</head>
<body>

<h1>⚔️ Grid Battle Simulator</h1>

<div id="controls">
    <button id="startBtn" onclick="startSimulation()">▶ Start Battle</button>
    <button id="resetBtn" onclick="resetBattle()">↺ Reset</button>
    <div id="speedControl">
        <label>Speed:</label>
        <input type="range" id="speedSlider" min="1" max="10" value="5">
        <span id="speedLabel">5x</span>
    </div>
</div>

<div id="battleInfo">
    <div class="team-info team1-info" id="team1Info">🔵 Team 1: 0 alive</div>
    <div id="statusBar">⏳ Place troops & press Start</div>
    <div class="team-info team2-info" id="team2Info">🔴 Team 2: 0 alive</div>
</div>

<div id="grid"></div>

<div id="log"><div>Battle log will appear here...</div></div>

<div id="troopSidebar" id="troopCards"></div>

<script>
// ============================================
// AJAX CALL 1 PRESET - Troop Definitions
// ============================================
// This array will later come from: $.ajax -> PHP -> MySQLi
const TROOP_DEFINITIONS = [
    {
        id: 1,
        name: "Knight",
        model: "⚔️",
        hp: 150,
        attack: 25,
        speed: 1,
        range: 1,
        attackSpeed: 1,     // attacks per tick cycle
        attackType: "melee"
    },
    {
        id: 2,
        name: "Archer",
        model: "🏹",
        hp: 80,
        attack: 20,
        speed: 1,
        range: 3,
        attackSpeed: 1,
        attackType: "ranged"
    },
    {
        id: 3,
        name: "Tank",
        model: "🛡️",
        hp: 300,
        attack: 15,
        speed: 1,
        range: 1,
        attackSpeed: 1,
        attackType: "melee"
    },
    {
        id: 4,
        name: "Mage",
        model: "🔮",
        hp: 70,
        attack: 35,
        speed: 1,
        range: 2,
        attackSpeed: 1,
        attackType: "ranged"
    },
    {
        id: 5,
        name: "Spearman",
        model: "🗡️",
        hp: 100,
        attack: 20,
        speed: 2,
        range: 1,
        attackSpeed: 1,
        attackType: "melee"
    },
    {
        id: 6,
        name: "Healer",
        model: "💚",
        hp: 60,
        attack: 10,
        speed: 1,
        range: 1,
        attackSpeed: 1,
        attackType: "melee"
    },
    {
        id: 7,
        name: "Cavalry",
        model: "🐴",
        hp: 120,
        attack: 30,
        speed: 3,
        range: 1,
        attackSpeed: 1,
        attackType: "melee"
    },
    {
        id: 8,
        name: "Cannon",
        model: "💣",
        hp: 50,
        attack: 50,
        speed: 0,
        range: 4,
        attackSpeed: 1,
        attackType: "ranged"
    }
];

// ============================================
// AJAX CALL 2 PRESET - Battle Setup / Placements
// ============================================
// This array will later come from: $.ajax -> PHP -> MySQLi
// posX: 0-9 (column), posY: 0-9 (row)
// Team 1: rows 7,8,9 (bottom 3 rows)
// Team 2: rows 0,1,2 (top 3 rows)
const DEFAULT_BATTLE_SETUP = {
    team1: {
        name: "Blue Legion",
        placements: [
            { troopId: 1, posX: 2, posY: 9 },
            { troopId: 1, posX: 7, posY: 9 },
            { troopId: 2, posX: 1, posY: 8 },
            { troopId: 2, posX: 8, posY: 8 },
            { troopId: 3, posX: 4, posY: 9 },
            { troopId: 3, posX: 5, posY: 9 },
            { troopId: 4, posX: 3, posY: 8 },
            { troopId: 5, posX: 6, posY: 7 },
            { troopId: 7, posX: 0, posY: 7 },
            { troopId: 8, posX: 5, posY: 8 }
        ]
    },
    team2: {
        name: "Red Horde",
        placements: [
            { troopId: 1, posX: 3, posY: 0 },
            { troopId: 1, posX: 6, posY: 0 },
            { troopId: 2, posX: 2, posY: 1 },
            { troopId: 2, posX: 7, posY: 1 },
            { troopId: 3, posX: 4, posY: 0 },
            { troopId: 3, posX: 5, posY: 0 },
            { troopId: 4, posX: 8, posY: 1 },
            { troopId: 5, posX: 3, posY: 2 },
            { troopId: 7, posX: 9, posY: 2 },
            { troopId: 8, posX: 4, posY: 1 }
        ]
    }
};

let BATTLE_SETUP = JSON.parse(JSON.stringify(DEFAULT_BATTLE_SETUP));

async function fetchDeploymentState() {
    try {
        const response = await fetch("{{ route('battleground.state') }}");
        if (!response.ok) {
            throw new Error('Failed to load deployment state');
        }

        const data = await response.json();

        return {
            deployments: Array.isArray(data.deployments) ? data.deployments : [],
            teams: data.teams || { team1: { id: null, name: 'Team 1' }, team2: { id: null, name: 'Team 2' } },
            currentBattle: data.current_battle || null,
        };
    } catch (error) {
        console.warn('Could not fetch battleground state, using fallback static setup:', error);
        return {
            deployments: [],
            teams: { team1: { id: null, name: 'Team 1' }, team2: { id: null, name: 'Team 2' } },
            currentBattle: null,
        };
    }
}

function buildTroopsFromDeployments(deployments, teamNames = { team1: { name: 'Team 1' }, team2: { name: 'Team 2' } }) {
    const mapped = [];
    const teams = {
        team1: [],
        team2: []
    };

    if (!Array.isArray(deployments)) {
        return { troops: mapped, teams };
    }

    const roleIconMap = {
        warrior: '🗡️',
        knight: '🛡️',
        archer: '🏹',
        mage: '🪄',
        healer: '💉',
        assassin: '🗡️',
        tank: '🛡️',
        support: '✨',
        rogue: '🥷',
        default: '⚔️'
    };

    deployments.forEach(dep => {
        const character = dep.character || TROOP_DEFINITIONS.find(t => t.id === dep.character_id);
        const hp = dep.current_hp != null ? dep.current_hp : (character?.hp ?? 100);

        const teamId = dep.team_id;
        const teamName = teamNames[teamId] || `Team ${teamId}`;

        const role = (character?.role || character?.role_name || '').toString().toLowerCase();
        const roleIcon = roleIconMap[role] || roleIconMap.default || (character?.image || '⚔️');

        const troop = {
            uid: nextUid++,
            team: teamId,
            teamName,
            id: character?.id ?? dep.character_id,
            name: character?.name ?? 'Unknown',
            model: roleIcon,
            role: character?.role ?? character?.role_name ?? 'Unknown',
            hp: Math.max(0, hp),
            maxHp: character?.hp ?? 100,
            attack: character?.damage ?? 10,
            speed: character?.speed ?? 1,
            range: character?.range ?? 1,
            attackSpeed: character?.cooldown > 0 ? character.cooldown : 1,
            attackType: character?.attackType || 'melee',
            posX: dep.grid_x ?? 0,
            posY: dep.grid_y ?? 0,
            alive: (hp > 0),
            attackCooldown: 0,
            movementCooldown: 0,
            totalDamageDealt: 0
        };

        mapped.push(troop);

        if (!teams[teamId]) {
            teams[teamId] = {
                name: teamName,
                troops: []
            };
        }
        teams[teamId].troops.push(troop);
    });

    return { troops: mapped, teams };
}

function applyDeploymentState(deployments) {
    if (!deployments || !deployments.length) {
        BATTLE_SETUP = JSON.parse(JSON.stringify(DEFAULT_BATTLE_SETUP));
        return false;
    }

    BATTLE_SETUP = {
        team1: { name: 'Team 1', placements: [] },
        team2: { name: 'Team 2', placements: [] }
    };

    deployments.forEach(dep => {
        const placement = { troopId: dep.character_id || 0, posX: dep.grid_x ?? 0, posY: dep.grid_y ?? 0 };
        if (dep.team_id == 2) {
            BATTLE_SETUP.team2.placements.push(placement);
        } else {
            BATTLE_SETUP.team1.placements.push(placement);
        }
    });

    return true;
}

// ============================================
// GAME ENGINE
// ============================================

const GRID_SIZE = 10;
let troops = [];
let tickInterval = null;
let tickCount = 0;
let battleOver = false;
let damageDealt = { team1: 0, team2: 0 };

// Unique troop instance ID
let nextUid = 1;

async function initBattle() {
    troops = [];
    tickCount = 0;
    battleOver = false;
    damageDealt = { team1: 0, team2: 0 };
    nextUid = 1;

    const state = await fetchDeploymentState();
    const deployments = state.deployments || [];

    if (state.teams) {
        BATTLE_SETUP.team1.name = state.teams.team1?.name || 'Team 1';
        BATTLE_SETUP.team2.name = state.teams.team2?.name || 'Team 2';
        window.battlegroundTeams = {
            team1: [],
            team2: [],
        };
    }

    const teamNamesById = {};
    if (state.teams.team1?.id) {
        teamNamesById[state.teams.team1.id] = state.teams.team1.name;
    }
    if (state.teams.team2?.id) {
        teamNamesById[state.teams.team2.id] = state.teams.team2.name;
    }

    if (deployments.length > 0) {
        const troopsData = buildTroopsFromDeployments(deployments, teamNamesById);
        troops = troopsData.troops;
        window.battlegroundTeams = troopsData.teams;
    } else {
        // Fallback static placement
        window.battlegroundTeams = { team1: [], team2: [] };
        BATTLE_SETUP = JSON.parse(JSON.stringify(DEFAULT_BATTLE_SETUP));

        BATTLE_SETUP.team1.placements.forEach(p => {
            const def = TROOP_DEFINITIONS.find(d => d.id === p.troopId);
            if (!def) return;
            troops.push({
                uid: nextUid++,
                team: 1,
                id: def.id,
                name: def.name,
                model: def.model,
                hp: def.hp,
                maxHp: def.hp,
                attack: def.attack,
                speed: def.speed,
                range: def.range,
                attackSpeed: def.attackSpeed,
                attackType: def.attackType,
                posX: p.posX,
                posY: p.posY,
                alive: true,
                attackCooldown: 0,
                totalDamageDealt: 0
            });
        });

        BATTLE_SETUP.team2.placements.forEach(p => {
            const def = TROOP_DEFINITIONS.find(d => d.id === p.troopId);
            if (!def) return;
            troops.push({
                uid: nextUid++,
                team: 2,
                id: def.id,
                name: def.name,
                model: def.model,
                hp: def.hp,
                maxHp: def.hp,
                attack: def.attack,
                speed: def.speed,
                range: def.range,
                attackSpeed: def.attackSpeed,
                attackType: def.attackType,
                posX: p.posX,
                posY: p.posY,
                alive: true,
                attackCooldown: 0,
                totalDamageDealt: 0
            });
        });

        window.battlegroundTeams.team1 = troops.filter(t => t.team === 1);
        window.battlegroundTeams.team2 = troops.filter(t => t.team === 2);
    }

    renderGrid();
    renderInfo();
    renderTroopCards();
    clearLog();
    addLog("Battle initialized. Press Start!", "");
}

// ---- DISTANCE ----
function getDistance(a, b) {
    // Chebyshev distance (diagonal = 1 step)
    return Math.max(Math.abs(a.posX - b.posX), Math.abs(a.posY - b.posY));
}

// ---- FIND NEAREST ENEMY ----
function findNearestEnemy(troop) {
    let nearest = null;
    let minDist = Infinity;
    troops.forEach(t => {
        if (t.team === troop.team || !t.alive) return;
        const d = getDistance(troop, t);
        if (d < minDist) {
            minDist = d;
            nearest = t;
        }
    });
    return nearest;
}

// ---- MOVE TOWARD TARGET ----
// Speed represents the number of ticks required to move one cell
// Movement only happens when movementCooldown <= 0
function moveToward(troop, target) {
    // Check if troop can move this tick
    if (troop.movementCooldown > 0) {
        troop.movementCooldown--;
        return false; // Can't move yet
    }

    if (troop.speed <= 0) return false;

    let dx = target.posX - troop.posX;
    let dy = target.posY - troop.posY;

    if (dx === 0 && dy === 0) return false;

    let stepX = dx === 0 ? 0 : (dx > 0 ? 1 : -1);
    let stepY = dy === 0 ? 0 : (dy > 0 ? 1 : -1);

    let newX = troop.posX + stepX;
    let newY = troop.posY + stepY;

    // Check boundaries
    newX = Math.max(0, Math.min(GRID_SIZE - 1, newX));
    newY = Math.max(0, Math.min(GRID_SIZE - 1, newY));

    // Check if cell is occupied by a friendly troop
    const occupant = troops.find(t =>
        t.alive && t.uid !== troop.uid &&
        t.posX === newX && t.posY === newY
    );

    if (occupant) {
        // Try horizontal only
        let altX = troop.posX + stepX;
        let altY = troop.posY;
        altX = Math.max(0, Math.min(GRID_SIZE - 1, altX));
        const occ2 = troops.find(t =>
            t.alive && t.uid !== troop.uid &&
            t.posX === altX && t.posY === altY
        );
        if (!occ2 && (altX !== troop.posX || altY !== troop.posY)) {
            newX = altX;
            newY = altY;
        } else {
            // Try vertical only
            let altX2 = troop.posX;
            let altY2 = troop.posY + stepY;
            altY2 = Math.max(0, Math.min(GRID_SIZE - 1, altY2));
            const occ3 = troops.find(t =>
                t.alive && t.uid !== troop.uid &&
                t.posX === altX2 && t.posY === altY2
            );
            if (!occ3 && (altX2 !== troop.posX || altY2 !== troop.posY)) {
                newX = altX2;
                newY = altY2;
            } else {
                return false; // stuck
            }
        }
    }

    if (newX === troop.posX && newY === troop.posY) return false;

    // Move exactly 1 cell
    troop.posX = newX;
    troop.posY = newY;

    // Reset movement cooldown to speed - 1 (will decrement next tick)
    // This means: speed=1 moves every tick, speed=2 moves every 2 ticks, etc.
    troop.movementCooldown = troop.speed - 1;

    return true;
}

// ---- SINGLE TICK ----
function tick() {
    if (battleOver) return;

    tickCount++;
    let tickLogs = [];

    // Process each alive troop
    const aliveTroops = troops.filter(t => t.alive);

    // Shuffle to avoid bias
    const shuffled = [...aliveTroops].sort(() => Math.random() - 0.5);

    const pendingDamage = []; // {target uid, damage, attacker}

    shuffled.forEach(troop => {
        if (!troop.alive) return;

        const enemy = findNearestEnemy(troop);
        if (!enemy) return;

        const dist = getDistance(troop, enemy);

        // Check if nearest enemy is within range
        if (dist <= troop.range) {
            // ATTACK - only one character at a time (the nearest one)
            if (troop.attackCooldown <= 0) {
                pendingDamage.push({
                    attackerUid: troop.uid,
                    targetUid: enemy.uid,
                    damage: troop.attack,
                    attackerName: troop.name,
                    targetName: enemy.name,
                    attackerTeam: troop.team
                });
                troop.attackCooldown = troop.attackSpeed;
                tickLogs.push({
                    msg: `${troop.model} ${troop.name}(T${troop.team}) is attacking ${enemy.model} ${enemy.name}(T${enemy.team}) [Range: ${troop.range}]`,
                    cls: "log-targeting"
                });
            } else {
                troop.attackCooldown--;
            }
        } else {
            // MOVE toward enemy (out of range)
            const moved = moveToward(troop, enemy);
            if (moved) {
                tickLogs.push({
                    msg: `${troop.model} ${troop.name}(T${troop.team}) moved toward ${enemy.model} ${enemy.name} [Distance: ${dist}/${troop.range}]`,
                    cls: "log-move"
                });
            }
            // Decrease cooldown even while moving
            if (troop.attackCooldown > 0) troop.attackCooldown--;
        }
    });

    // Apply all damage simultaneously
    pendingDamage.forEach(pd => {
        const target = troops.find(t => t.uid === pd.targetUid);
        const attacker = troops.find(t => t.uid === pd.attackerUid);
        if (!target || !attacker) return;

        target.hp -= pd.damage;
        attacker.totalDamageDealt += pd.damage;

        if (pd.attackerTeam === 1) damageDealt.team1 += pd.damage;
        else damageDealt.team2 += pd.damage;

        // Flash damaged target
        flashCell(target.posX, target.posY, "cell-damaged");

        tickLogs.push({
            msg: `${attacker.model} ${pd.attackerName}(T${pd.attackerTeam}) ⚔️ ${target.model} ${pd.targetName}(T${target.team}) for ${pd.damage} dmg [${Math.max(0, target.hp)}/${target.maxHp}]`,
            cls: "log-attack"
        });

        if (target.hp <= 0) {
            target.alive = false;
            target.hp = 0;
            flashCell(target.posX, target.posY, "cell-death");
            tickLogs.push({
                msg: `💀 ${target.model} ${pd.targetName}(T${target.team}) has been SLAIN!`,
                cls: "log-kill"
            });
        }
    });

    // Render
    renderGrid();
    renderInfo();

    // Log
    tickLogs.forEach(l => addLog(`[T${tickCount}] ${l.msg}`, l.cls));

    // Check win condition
    checkWinCondition();
}

function checkWinCondition() {
    // Ensure a minimum number of ticks before win is declared (prevents immediate step-out wins)
    if (tickCount < 2) {
        return;
    }

    const teamGroups = troops.reduce((acc, troop) => {
        if (!troop.alive) return acc;
        if (!acc[troop.team]) {
            acc[troop.team] = { 
                id: troop.team,
                name: troop.teamName || `Team ${troop.team}`,
                alive: 0,
                totalDamage: 0
            };
        }
        acc[troop.team].alive += 1;
        acc[troop.team].totalDamage += troop.totalDamageDealt || 0;
        return acc;
    }, {});

    const aliveTeams = Object.values(teamGroups).filter(t => t.alive > 0);

    if (aliveTeams.length === 0) {
        // all dead -> draw
        battleOver = true;
        stopSimulation();
        const winner = '⚖️ All teams are extinct. Draw!';
        addLog(winner, 'log-win');
        document.getElementById('statusBar').textContent = winner;
        return;
    }

    if (aliveTeams.length > 1) {
        return; // battle continues until single team remains
    }

    // victorious team found
    const winnerTeam = aliveTeams[0];
    battleOver = true;
    stopSimulation();
    const msg = `🏆 ${winnerTeam.name} WINS! (${winnerTeam.alive} troops remaining)`;
    addLog(msg, 'log-win');
    document.getElementById('statusBar').textContent = msg;
}

// ============================================
// SIMULATION CONTROLS
// ============================================

function startSimulation() {
    if (battleOver) return;
    if (tickInterval) return;

    const speed = parseInt(document.getElementById("speedSlider").value);
    const ms = Math.max(50, 600 - (speed * 55));

    tickInterval = setInterval(tick, ms);
    document.getElementById("statusBar").textContent = "⚔️ Battle in progress...";
    document.getElementById("startBtn").textContent = "⏸ Pause";
    document.getElementById("startBtn").onclick = pauseSimulation;
}

function pauseSimulation() {
    stopSimulation();
    document.getElementById("statusBar").textContent = "⏸ Paused";
    document.getElementById("startBtn").textContent = "▶ Resume";
    document.getElementById("startBtn").onclick = startSimulation;
}

function stopSimulation() {
    if (tickInterval) {
        clearInterval(tickInterval);
        tickInterval = null;
    }
}

async function resetBattle() {
    stopSimulation();
    document.getElementById("startBtn").textContent = "▶ Start Battle";
    document.getElementById("startBtn").onclick = startSimulation;
    await initBattle();
}

// Speed slider
document.getElementById("speedSlider").addEventListener("input", function () {
    document.getElementById("speedLabel").textContent = this.value + "x";
    if (tickInterval) {
        stopSimulation();
        startSimulation();
    }
});

// ============================================
// RENDERING
// ============================================

function renderGrid() {
    const gridEl = document.getElementById("grid");
    gridEl.innerHTML = "";

    for (let y = 0; y < GRID_SIZE; y++) {
        for (let x = 0; x < GRID_SIZE; x++) {
            const cell = document.createElement("div");
            cell.classList.add("cell");
            cell.id = `cell-${x}-${y}`;

            // Zone coloring
            if (y >= 7) cell.classList.add("cell-team1-zone");
            else if (y <= 2) cell.classList.add("cell-team2-zone");
            else cell.classList.add("cell-neutral");

            // Check for troop
            const troop = troops.find(t => t.alive && t.posX === x && t.posY === y);

            if (troop) {
                cell.classList.add(troop.team === 1 ? "team1-troop" : "team2-troop");

                const icon = document.createElement("div");
                icon.className = "troop-icon";
                icon.textContent = troop.model;
                cell.appendChild(icon);

                const nameEl = document.createElement("div");
                nameEl.className = "troop-name";
                nameEl.textContent = troop.name;
                cell.appendChild(nameEl);

                // HP bar
                const hpBg = document.createElement("div");
                hpBg.className = "hp-bar-bg";
                const hpBar = document.createElement("div");
                hpBar.className = `hp-bar ${troop.team === 1 ? "hp-bar-team1" : "hp-bar-team2"}`;
                hpBar.style.width = Math.max(0, (troop.hp / troop.maxHp) * 100) + "%";
                hpBg.appendChild(hpBar);
                cell.appendChild(hpBg);
            }

            gridEl.appendChild(cell);
        }
    }
}

function flashCell(x, y, className) {
    setTimeout(() => {
        const cell = document.getElementById(`cell-${x}-${y}`);
        if (cell) {
            cell.classList.add(className);
            setTimeout(() => cell.classList.remove(className), 400);
        }
    }, 10);
}

function renderInfo() {
    const grouped = troops.reduce((map, troop) => {
        if (!map[troop.team]) {
            map[troop.team] = {
                name: troop.teamName || `Team ${troop.team}`,
                total: 0,
                alive: 0,
                damage: 0
            };
        }
        map[troop.team].total++;
        if (troop.alive) map[troop.team].alive++;
        map[troop.team].damage += troop.totalDamageDealt || 0;
        return map;
    }, {});

    const teamKeys = Object.keys(grouped).sort((a,b)=>Number(a)-Number(b));

    const team1Info = document.getElementById("team1Info");
    const team2Info = document.getElementById("team2Info");

    if (teamKeys[0]) {
        const t = grouped[teamKeys[0]];
        team1Info.innerHTML = `🔵 ${t.name}: ${t.alive}/${t.total} | Dmg: ${t.damage}`;
    } else {
        team1Info.innerHTML = '🔵 Team: 0/0 | Dmg: 0';
    }

    if (teamKeys[1]) {
        const t = grouped[teamKeys[1]];
        team2Info.innerHTML = `🔴 ${t.name}: ${t.alive}/${t.total} | Dmg: ${t.damage}`;
    } else {
        team2Info.innerHTML = '🔴 Team: 0/0 | Dmg: 0';
    }
}

function renderTroopCards() {
    const sidebar = document.getElementById("troopSidebar");
    sidebar.innerHTML = "";

    troops.forEach(t => {
        const card = document.createElement("div");
        card.className = `troop-card team${t.team}`;
        card.innerHTML = `
            <div class="card-name">${t.model} ${t.name} <small>(T${t.team})</small></div>
            <div class="card-stat">HP: ${t.hp}/${t.maxHp} | ATK: ${t.attack}</div>
            <div class="card-stat">SPD: ${t.speed} | RNG: ${t.range} | ${t.attackType}</div>
        `;
        sidebar.appendChild(card);
    });
}

// ---- LOG ----
function addLog(msg, cls) {
    const log = document.getElementById("log");
    const div = document.createElement("div");
    div.className = cls;
    div.textContent = msg;
    log.prepend(div);

    // Keep log manageable
    while (log.children.length > 200) {
        log.removeChild(log.lastChild);
    }
}

function clearLog() {
    document.getElementById("log").innerHTML = "";
}

// ============================================
// INIT
// ============================================
(async () => {
    await initBattle();
})();

</script>
</body>
</html>
