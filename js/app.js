// ====== CONFIG ======
const BASE_URL = '/scisolve/backend'; 


async function fetchJSON(path, options = {}) {
  const res = await fetch(`${BASE_URL}${path}`, {
    headers: { 'Content-Type': 'application/json' },
    // ...FETCH_OPTS,
    ...options
  });
  let data;
  try { data = await res.json(); } catch(e){ data = { ok: false, error: 'Invalid JSON' }; }
  if (!res.ok || data.ok === false) {
    const msg = (data && data.error) ? data.error : `HTTP ${res.status}`;
    throw new Error(msg);
  }
  return data.data;
}

// ====== GLOBAL STATE ======
const UI = {
  user: null,               // me.php থেকে আসবে
  sections: {},             // DOM refs
  loaders: {},              // লোডার/spinner refs
};

// ====== SECTION SHOW/HIDE ======
function qs(sel){ return document.querySelector(sel); }
function qsa(sel){ return document.querySelectorAll(sel); }

function showSection(id){
  // id = 'admin', 'expert', 'user', 'login', 'challenges', 'simulations' ইত্যাদি
  qsa('[data-section]').forEach(el => el.classList.add('hidden'));
  const el = qs(`[data-section="${id}"]`);
  if (el) el.classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', init);

async function init(){
  try {
    const me = await fetchJSON('/auth/me.php'); // সেশন থাকলে ইউজার ডাটা
    UI.user = me;              // { user_id, name, email, role, score }
    applyRoleUI(me.role);      // নিচে আছে
    await bootstrapData(me.role);
  } catch (e) {
    // Not logged in
    UI.user = null;
    showSection('login');      // লগইন সেকশন দেখান
  }
}

function applyRoleUI(role){
  // সব role-স্পেসিফিক মেনু/সেকশন প্রথমে hide
  qsa('[data-role-only]').forEach(el => el.classList.add('hidden'));
  // তারপর এই role-এরগুলো দেখান
  qsa(`[data-role-only="${role}"]`).forEach(el => el.classList.remove('hidden'));

  // ড্যাশবোর্ড ল্যান্ডিং
  if(role === 'admin')       showSection('admin');
  else if(role === 'domain_expert') showSection('expert');
  else                       showSection('user'); // সাধারণ ইউজার
}

// Login form bind
const loginForm = document.getElementById('email-login');
if (loginForm) {
  loginForm.addEventListener('submit', onLoginSubmit);
}

async function onLoginSubmit(e){
  e.preventDefault();
  const email = qs('#login-email').value.trim();
  const password = qs('#login-password').value;
  setLoading(true, 'login');

  try {
    await fetchJSON('/auth/login.php', {
      method: 'POST',
      body: JSON.stringify({ email, password })
    });
    // সফল হলে ইউজার/রোল আবার নিন, UI রিফ্রেশ
    const me = await fetchJSON('/auth/me.php');
    UI.user = me;
    applyRoleUI(me.role);
    await bootstrapData(me.role);
  } catch (err) {
    showError('#login-error', err.message);
  } finally {
    setLoading(false, 'login');
  }
}

function setLoading(on, key){
  // খুব সিম্পল: যেকোনো লোডিং স্পিনার থাকলে শো/হাইড করুন
  // উদাহরণ: <div id="login-loading" class="hidden">Loading...</div>
  const el = document.getElementById(`${key}-loading`);
  if (el) el.classList.toggle('hidden', !on);
}
function showError(sel, msg){
  const el = qs(sel);
  if (!el) return;
  el.textContent = msg;
  el.classList.remove('hidden');
}

const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', async ()=>{
    await fetch(`${BASE_URL}/auth/logout.php`, { method: 'POST' });
    UI.user = null;
    showSection('login');
  });
}
async function loadChallenges(){
  const rows = await fetchJSON('/challenges/get.php');
  const tbody = document.getElementById('challenges-body');
  tbody.innerHTML = ''; // reset
  for (const r of rows) {
    const tr = document.createElement('tr');

    // নিরাপদে textContent ব্যবহার করুন
    const tdTitle = document.createElement('td'); tdTitle.textContent = r.title;
    const tdCat   = document.createElement('td'); tdCat.textContent = r.category;
    const tdDiff  = document.createElement('td'); tdDiff.textContent = r.difficulty;

    const tdAct = document.createElement('td');
    if (UI.user && (UI.user.role === 'admin')) {
      const btn = document.createElement('button');
      btn.textContent = 'Delete';
      btn.dataset.id = r.challenge_id;
      btn.addEventListener('click', onDeleteChallenge);
      tdAct.appendChild(btn);
    } else {
      tdAct.textContent = '-';
    }

    tr.append(tdTitle, tdCat, tdDiff, tdAct);
    tbody.appendChild(tr);
  }
}

async function onDeleteChallenge(e){
  const id = +e.currentTarget.dataset.id;
  if (!confirm('Delete this challenge?')) return;
  await fetchJSON('/challenges/delete.php', {
    method: 'POST',
    body: JSON.stringify({ challenge_id: id })
  });
  await loadChallenges();
}

const chForm = document.getElementById('challenge-create');
if (chForm) {
  chForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const payload = {
      title: qs('#ch-title').value.trim(),
      category: qs('#ch-category').value.trim(),
      difficulty: qs('#ch-difficulty').value,
      description: qs('#ch-desc').value
    };
    await fetchJSON('/challenges/add.php', { method:'POST', body: JSON.stringify(payload) });
    chForm.reset();
    await loadChallenges();
  });
}

const runBtn = document.getElementById('run-sim-btn');
if (runBtn) {
  runBtn.addEventListener('click', onRunSimulation);
}

async function onRunSimulation(){
  try {
    await fetchJSON('/simulations/run.php', { method:'POST' });
    qs('#run-sim-msg').textContent = 'Simulation started (running)...';
  } catch (e) {
    qs('#run-sim-msg').textContent = 'Failed: ' + e.message;
  }
}

async function loadSimulations(){
  const rows = await fetchJSON('/simulations/get.php'); // admin/expert only
  const tbody = document.getElementById('sims-body');
  tbody.innerHTML = '';
  for (const r of rows) {
    const tr = document.createElement('tr');
    const tdId = document.createElement('td'); tdId.textContent = r.simulation_id;
    const tdName = document.createElement('td'); tdName.textContent = r.name;
    const tdEmail = document.createElement('td'); tdEmail.textContent = r.email;

    const tdStatus = document.createElement('td'); tdStatus.textContent = r.status;

    const tdUpdate = document.createElement('td');
    const sel = document.createElement('select');
    ['running','completed','failed'].forEach(s=>{
      const o = document.createElement('option'); o.value=s; o.textContent=s;
      if (s === r.status) o.selected = true;
      sel.appendChild(o);
    });
    const btn = document.createElement('button'); btn.textContent = 'Save';
    const resInput = document.createElement('input'); resInput.placeholder = 'Result (JSON/Text)';
    btn.addEventListener('click', async ()=>{
      await fetchJSON('/simulations/update.php', {
        method: 'POST',
        body: JSON.stringify({
          simulation_id: r.simulation_id,
          status: sel.value,
          result: resInput.value || null
        })
      });
      await loadSimulations();
    });

    tdUpdate.append(sel, resInput, btn);

    tr.append(tdId, tdName, tdEmail, tdStatus, tdUpdate);
    tbody.appendChild(tr);
  }
}

async function bootstrapData(role){
  // লগইনের পর/পেজ লোডে একবার করে ডেটা লোডিং
  try {
    await loadChallenges(); // সবাই দেখতে পাবে
  } catch(e){ console.warn('Challenges load:', e.message); }

  if (role === 'admin' || role === 'domain_expert') {
    try { await loadSimulations(); } catch(e){ console.warn('Sims load:', e.message); }
    showSection(role === 'admin' ? 'admin' : 'expert');
  } else {
    showSection('user');
  }
}
