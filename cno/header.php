<?php
// header.php for CNO
?>
<style>
/* Header bar */
.topbar {
  background: white;
  border-bottom: 1px solid #ccc;
  padding: 8px 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 1000;
  position: relative;
}
.logo {
    display: flex;
    align-items: center;
    font-weight: bold;
    font-size: 24px;
    color: #333;
}

.logo img {
    height: 40px;
    margin-right: 10px;
}

.logo .cno-color {
    color: #00a0a0;
}

.logo-space {
    margin-right: 8px;
}
.brand {
  display: flex;
  align-items: center;
  font-weight: bold;
  font-size: 18px;
  cursor: pointer;
}

.brand i {
  font-size: 20px;
  margin-right: 8px;
}

.brand .cno {
  color: #009688; /* green */
  margin-right: 4px;
}

.brand .nutrimap {
  color: #000; /* black */
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.searchbox {
  position: relative;
}

.searchbox input {
  padding: 8px 30px 8px 30px;
  border: 1px solid #aaa;
  border-radius: 4px;
  width: 220px;
  font-size: 14px;
  outline: none;
}

.searchbox i {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
}

.bell {
  font-size: 20px;
  cursor: pointer;
  color: #333;
  position: relative;
}

/* Badge for unread notifications */
#notificationBadge {
  position: absolute; top: -5px; right: -5px;
  background: red; color: white; font-size: 10px;
  padding: 2px 5px; border-radius: 50%;
  display: none;
}

/* Bell pulse animation when new notification arrives */
@keyframes bellPulse {
  0% { transform: scale(1); color: #333; }
  25% { transform: scale(1.2); color: #ff4444; }
  50% { transform: scale(1); color: #ff4444; }
  75% { transform: scale(1.2); color: #ff4444; }
  100% { transform: scale(1); color: #333; }
}

.bell.pulse i {
  animation: bellPulse 0.8s ease;
}

/* Container for dynamic side menu */
#sidemenu-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 0;
  height: 100%;
  overflow: hidden;
  z-index: 2000;
}

/* ===== Notification Sidebar (right side) ===== */
#notifSidebar {
  position: fixed;
  top: 0;
  right: -400px; /* hidden by default */
  width: 380px;
  height: 100%;
  background: #fff;
  border-left: 1px solid #ccc;
  box-shadow: -2px 0 8px rgba(0,0,0,0.15);
  z-index: 1000;
  padding: 15px;
  overflow-y: auto;
  transition: right 0.3s ease;
  display: flex;
  flex-direction: column;
}

#notifSidebar.open { right: 0; }

#notifSidebarHeader {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
}

#closeNotifSidebar {
  font-size: 26px;
  cursor: pointer;
}

#notifSidebar table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

#notifSidebar th, #notifSidebar td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
  text-align: left;
}

#notifSidebar button {
  padding: 6px 12px;
  margin: 0 2px;
  cursor: pointer;
}
</style>

<header class="topbar">
  <div class="brand" id="menuBtn">
    <i class="fa fa-bars"></i>
     <div class="logo">
          <img src="../img/CNO_Logo.png" alt="CNO NutriMap Logo">
            <span class="cno-color">CNO</span><span class="logo-space"></span><span>NutriMap</span>
        </div>
  </div>
  <div class="topbar-right">
    <!-- Notification Bell with badge -->
    <div class="bell" id="bellBtn" style="position: relative;">
      <i class="fa fa-bell"></i>
      <span id="notificationBadge"></span>
    </div>
  </div>
</header>

<div id="sidemenu-container"></div>

<!-- Notification Sidebar -->
<div id="notifSidebar">
  <div id="notifSidebarHeader">
    <h2>Notifications</h2>
    <span id="closeNotifSidebar">&times;</span>
  </div>
  <div style="margin: 10px 0; display: flex; justify-content: space-between;">
    <button id="markAllReadBtn">Mark All as Read</button>
    <button id="filterUnreadBtn">Show Unread</button>
    <button id="showAllBtn" style="display:none;">Show All</button>
  </div>
  <table>
    <thead>
      <tr>
        <th>Message</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <div style="margin-top: 10px; display: flex; justify-content: center; gap: 10px;">
    <button id="prevPage">Previous</button>
    <button id="nextPage">Next</button>
  </div>
</div>

<script>
document.getElementById('menuBtn').addEventListener('click', async () => {
    const container = document.getElementById('sidemenu-container');

    if (!container.innerHTML.trim()) {
        // Load CNO sidebar
        const response = await fetch('sidebar.php');
        const html = await response.text();
        container.innerHTML = html;

        const menu = document.getElementById('sideMenu');
        if (!menu) return;

        // Close button
        const closeBtn = menu.querySelector('.close-btn');
        if (closeBtn) closeBtn.addEventListener('click', () => menu.classList.remove('open'));

        // Menu items
        const menuItems = menu.querySelectorAll('.menu-links li[data-url]');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                window.location.href = item.getAttribute('data-url');
                menu.classList.remove('open');
            });
        });

        // Settings dropdown
        const settingsBtn = menu.querySelector('#settingsBtn');
        const settingsMenu = menu.querySelector('#settingsMenu');
        if (settingsBtn && settingsMenu) {
            settingsBtn.addEventListener('click', e => {
                e.preventDefault();
                settingsBtn.classList.toggle('open');
                settingsMenu.style.display = settingsMenu.style.display === 'flex' ? 'none' : 'flex';
            });
        }

        // Active page highlight
        const currentPage = window.location.pathname.split('/').pop();

        menuItems.forEach(li => {
            if (li.getAttribute('data-url') === currentPage) li.classList.add('active');
        });

        const settingsItems = settingsMenu.querySelectorAll('li[data-url]');
        settingsItems.forEach(li => {
            if (li.getAttribute('data-url') === currentPage) {
                li.classList.add('active');
                settingsBtn.classList.add('open');
                settingsMenu.style.display = 'flex';
            }
        });

        // Profile click
        const userProfileBtn = menu.querySelector('#userProfileBtn');
        if (userProfileBtn) {
            userProfileBtn.addEventListener('click', () => {
                window.location.href = 'profile.php';
                menu.classList.remove('open');
            });
        }
    }

    // Open sidebar after loading
    document.getElementById('sideMenu').classList.add('open');
});

// ===== Notification Sidebar Logic =====
let currentPage = 1;
const pageSize = 5;
let totalNotifications = 0;
let totalUnread = 0;
let showUnreadOnly = false;
let lastAlertedId = 0;
let initialized = false;
const basePath = 'notification/';
const notificationSound = new Audio(basePath + 'notification.wav');
const badge = document.getElementById('notificationBadge');
const bell = document.getElementById('bellBtn');
const notifSidebar = document.getElementById('notifSidebar');
const closeNotifSidebar = document.getElementById('closeNotifSidebar');
const btnPrev = document.getElementById('prevPage');
const btnNext = document.getElementById('nextPage');

// Toast container
let toastContainer = document.getElementById('toastContainer');
if (!toastContainer) {
  toastContainer = document.createElement('div');
  toastContainer.id = 'toastContainer';
  toastContainer.style.position = 'fixed';
  toastContainer.style.top = '20px';
  toastContainer.style.left = '50%';
  toastContainer.style.transform = 'translateX(-50%)';
  toastContainer.style.zIndex = '9999';
  document.body.appendChild(toastContainer);
}

// Unlock sound
document.addEventListener('click', () => {
  notificationSound.muted = true;
  notificationSound.play().then(() => {
    notificationSound.pause();
    notificationSound.muted = false;
  }).catch(() => {});
}, { once: true });

function updateBadge(count) {
  badge.style.display = count > 0 ? 'inline-block' : 'none';
  badge.innerText = count > 0 ? count : '';
}

async function fetchUnreadCount() {
  try {
    const res = await fetch(basePath + 'get_notifications_cno.php?count_unread=1');
    const data = await res.json();
    updateBadge(data.totalUnread);
  } catch (e) { console.error(e); }
}

let lastToastMessage = "";
function playNotificationEffect(msg = "New notification received!") {
  if (msg === lastToastMessage) return;
  lastToastMessage = msg;

  try { notificationSound.currentTime = 0; notificationSound.play().catch(()=>{}); } catch(e){}

  bell.classList.add('pulse');
  setTimeout(()=>bell.classList.remove('pulse'),1000);

  const toast = document.createElement('div');
  toast.innerText = `🔔 ${msg}`;
  toast.style.cssText="background:#333;color:#fff;padding:10px 16px;border-radius:8px;margin-top:8px;box-shadow:0 2px 6px rgba(0,0,0,0.2);opacity:0;transition:opacity 0.5s, transform 0.5s;transform:translateY(-20px)";
  toastContainer.appendChild(toast);
  setTimeout(()=>{toast.style.opacity='1'; toast.style.transform='translateY(0)';},100);
  setTimeout(()=>{toast.style.opacity='0'; toast.style.transform='translateY(-20px)'; setTimeout(()=>toast.remove(),500);},4000);
  setTimeout(()=>{lastToastMessage="";},8000);
}

let isLiveUpdate = false;
async function fetchNotifications() {
  try {
    const res = await fetch(basePath + `get_notifications_cno.php?page=${currentPage}&size=${pageSize}${showUnreadOnly?'&unread_only=1':''}`);
    const data = await res.json();
    totalNotifications = data.totalCount;
    totalUnread = data.totalUnread;

    const tbody = notifSidebar.querySelector('tbody');
    tbody.innerHTML = '';
    data.notifications.forEach(n=>{
      const tr = document.createElement('tr');
      tr.style.cursor = 'pointer';
      if(!n.read_status) tr.style.fontWeight='bold';
      tr.innerHTML = `<td>${n.message}</td><td>${n.date}</td><td>${n.read_status?'Read':'New'}</td>`;
      tr.onclick=()=>markAsRead(n.id);
      tbody.appendChild(tr);
    });

    const effectiveTotal = showUnreadOnly ? totalUnread : totalNotifications;
    btnPrev.disabled = currentPage <= 1;
    btnNext.disabled = (currentPage*pageSize) >= effectiveTotal;

    if(data.notifications.length>0){
      const newest=data.notifications[0];
      if(initialized && isLiveUpdate && newest.id>lastAlertedId) playNotificationEffect(newest.message);
      lastAlertedId=Math.max(lastAlertedId,newest.id);
    }
    if(!initialized) initialized=true; isLiveUpdate=false;
  } catch(e){console.error(e);}
}

btnPrev.addEventListener('click',()=>{if(currentPage>1){currentPage--; fetchNotifications();}});
btnNext.addEventListener('click',()=>{if((currentPage*pageSize)<(showUnreadOnly?totalUnread:totalNotifications)){currentPage++; fetchNotifications();}});

bell.addEventListener('click',()=>{
  currentPage=1; fetchNotifications(); fetchUnreadCount();
  notifSidebar.classList.add('open');
});

closeNotifSidebar.onclick = ()=>notifSidebar.classList.remove('open');

// Mark as read
async function markAsRead(id){
  try{
    const res=await fetch(basePath+'mark_as_read_cno.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`id=${id}`});
    const result=await res.json();
    if(result.status==='success'){fetchNotifications(); fetchUnreadCount();}
  } catch(e){console.error(e);}
}

// SSE
let eventSource;
function initSSE(){
  if(eventSource) eventSource.close();
  eventSource=new EventSource(basePath+'notifications_stream_cno.php');
  eventSource.onmessage=function(){isLiveUpdate=true; fetchUnreadCount(); fetchNotifications();}
  eventSource.onerror=function(){console.log('SSE lost, reconnecting...'); setTimeout(initSSE,3000);}
}
initSSE();

// Mark all / filter
document.getElementById('markAllReadBtn').addEventListener('click', async ()=>{
  try{ const res=await fetch(basePath+'mark_as_read_cno.php',{method:'POST'}); const d=await res.json(); if(d.status==='success'){fetchNotifications(); fetchUnreadCount();} }catch(e){console.error(e);}
});
document.getElementById('filterUnreadBtn').addEventListener('click',()=>{showUnreadOnly=true; document.getElementById('showAllBtn').style.display='inline-block'; document.getElementById('filterUnreadBtn').style.display='none'; currentPage=1; fetchNotifications();});
document.getElementById('showAllBtn').addEventListener('click',()=>{showUnreadOnly=false; document.getElementById('showAllBtn').style.display='none'; document.getElementById('filterUnreadBtn').style.display='inline-block'; currentPage=1; fetchNotifications();});

// Initialize
fetchUnreadCount(); fetchNotifications();
</script>
