<?php
// header.php
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
.logo .cno-color { color: #00a0a0; }
.logo-space { margin-right: 8px; }
.brand {
  display: flex;
  align-items: center;
  font-weight: bold;
  font-size: 18px;
  cursor: pointer;
}
.brand i { font-size: 20px; margin-right: 8px; }
.brand .cno { color: #009688; margin-right: 4px; }
.brand .nutrimap { color: #000; }
.topbar-right { display: flex; align-items: center; gap: 15px; }
.bell {
  font-size: 20px;
  cursor: pointer;
  color: #333;
  position: relative;
}
#notificationBadge {
  position: absolute; top: -5px; right: -5px;
  background: red; color: white; font-size: 10px;
  padding: 2px 5px; border-radius: 50%;
  display: none;
}
@keyframes bellPulse {
  0% { transform: scale(1); color: #333; }
  25% { transform: scale(1.2); color: #ff4444; }
  50% { transform: scale(1); color: #ff4444; }
  75% { transform: scale(1.2); color: #ff4444; }
  100% { transform: scale(1); color: #333; }
}
.bell.pulse i { animation: bellPulse 0.8s ease; }

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

/* Notification Sidebar - hidden by default */
#notifSidebar {
  position: fixed;
  top: 0;
  right: -400px; /* hidden off-screen initially */
  width: 380px;
  height: 100%;
  background: #fff;
  border-left: 1px solid #ccc;
  box-shadow: -2px 0 8px rgba(0,0,0,0.15);
  z-index: 1000;
  padding: 15px;
  overflow-y: auto;
  transition: right 0.3s ease;
}
#notifSidebar.open { right: 0; }

#notifSidebarHeader {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
}

#notificationTable td:first-child {
    max-width: 220px;   /* adjust width as needed */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
/* Notification Sidebar Table Styling */
#notificationTable {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 6px; /* adds space between rows */
}

#notificationTable thead th {
  text-align: left;
  font-weight: bold;
  padding: 8px 10px;
  border-bottom: 1px solid #ddd;
  background-color: #f9f9f9;
  font-size: 14px;
  color: #333;
}

#notificationTable tbody tr {
  background-color: #fff;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

#notificationTable tbody tr:hover {
  transform: translateY(-2px);
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

#notificationTable td {
  padding: 10px;
  font-size: 13px;
  color: #444;
  vertical-align: middle;
}

#notificationTable td:first-child {
  max-width: 220px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#notificationTable td:nth-child(2) {
  font-size: 12px;
  color: #888;
}

#notificationTable td:nth-child(3) {
  font-size: 12px;
  color: #fff;
  font-weight: bold;
  text-align: center;
  border-radius: 4px;
  padding: 4px 8px;
}

#notificationTable td:nth-child(3).Read {
  background-color: #838080ff;
}

#notificationTable td:nth-child(3).New {
  background-color: #979797ff;
}


#closeNotifSidebar { font-size: 26px; cursor: pointer; }
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

  <table id="notificationTable" style="width: 100%; border-collapse: collapse;">
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

<!-- Notification Detail Modal -->
<div id="notifModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
  <div style="background:#fff; padding:20px; border-radius:8px; width:400px; max-width:90%; position:relative; display:flex; flex-direction:column; justify-content:space-between; height:auto;">
    <span id="closeNotifModal" style="position:absolute; top:10px; right:15px; font-size:22px; cursor:pointer;">&times;</span>
    <h3 id="notifModalHeader" style="margin-bottom:15px;">Status</h3>
    <p id="notifModalMessage" style="white-space:pre-wrap; flex-grow:1;"></p>
    <div id="notifModalFooter" style="text-align:right; font-size:12px; color:#666; margin-top:15px;"></div>
  </div>
</div>

<script>
// Side menu logic
document.getElementById('menuBtn').addEventListener('click', async () => {
    const container = document.getElementById('sidemenu-container');
    if (!container.innerHTML.trim()) {
        const response = await fetch('sidemenu.php');
        const html = await response.text();
        container.innerHTML = html;

        const menu = document.getElementById('sideMenu');
        if (!menu) return;

        // Close button
        const closeBtn = menu.querySelector('.close-btn');
        if (closeBtn) closeBtn.addEventListener('click', () => menu.classList.remove('open'));

        // Menu items navigation
        const menuItems = menu.querySelectorAll('.menu-links li[data-url]');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                const url = item.getAttribute('data-url');
                if (url) window.location.href = url;
                menu.classList.remove('open');
            });
        });

        // Settings dropdown items
        const settingsItems = menu.querySelectorAll('#settingsMenu li[data-url]');
        settingsItems.forEach(item => {
            item.addEventListener('click', () => {
                const url = item.getAttribute('data-url');
                if (url) window.location.href = url;
                menu.classList.remove('open');
            });
        });

        // Profile button
        const profileBtn = menu.querySelector('#userProfileBtn');
        if (profileBtn) profileBtn.addEventListener('click', () => {
            window.location.href = 'profile.php';
            menu.classList.remove('open');
        });

        // Settings dropdown toggle
        const settingsBtn = menu.querySelector('#settingsBtn');
        const settingsMenu = menu.querySelector('#settingsMenu');
        if (settingsBtn && settingsMenu) {
            settingsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                settingsBtn.classList.toggle('open');
                settingsMenu.style.display = settingsMenu.style.display === 'flex' ? 'none' : 'flex';
            });
        }

        // ===================== ACTIVE PAGE HIGHLIGHT =====================
        const currentPage = window.location.pathname.split("/").pop(); // e.g., 'home.php'

        // Highlight main menu items
        menu.querySelectorAll('.menu-links li[data-url]').forEach(li => {
            if (li.getAttribute('data-url') === currentPage) {
                li.classList.add('active');
            }
        });

        // Highlight settings submenu
        menu.querySelectorAll('#settingsMenu li[data-url]').forEach(li => {
            if (li.getAttribute('data-url') === currentPage) {
                li.classList.add('active');
                // Open parent dropdown
                if (settingsBtn && settingsMenu) {
                    settingsBtn.classList.add('open');
                    settingsMenu.style.display = 'flex';
                }
            }
        });
        // =================================================================
    }

    const menu = document.getElementById('sideMenu');
    if (menu) menu.classList.add('open');
});

// Notifications
let currentPage = 1, pageSize = 5, totalNotifications = 0, totalUnread = 0;
let showUnreadOnly = false, lastAlertedId = 0, initialized = false, isLiveUpdate = false;
const basePath = 'notification/', notificationSound = new Audio(basePath+'notification.wav');
const badge = document.getElementById('notificationBadge');
const bell = document.getElementById('bellBtn');
const btnPrev = document.getElementById('prevPage');
const btnNext = document.getElementById('nextPage');
const notifSidebar = document.getElementById('notifSidebar');
const closeNotifSidebar = document.getElementById('closeNotifSidebar');

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

// Unlock sound on first click
document.addEventListener('click', () => {
  notificationSound.muted = true;
  notificationSound.play().then(()=>{notificationSound.pause();notificationSound.muted=false;}).catch(()=>{});
}, { once: true });

function updateBadge(count) {
  badge.style.display = count>0?'inline-block':'none';
  badge.innerText = count>0?count:'';
}

async function fetchUnreadCount() {
  try {
    const res = await fetch(basePath+'get_notifications.php?count_unread=1');
    const data = await res.json();
    updateBadge(data.totalUnread);
  } catch(e){console.error(e);}
}

function playNotificationEffect() {
  // Always show the same fixed message
  try { 
    notificationSound.currentTime = 0; 
    notificationSound.play().catch(()=>{}); 
  } catch(e){}

  bell.classList.add('pulse');
  setTimeout(()=>bell.classList.remove('pulse'),1000);

  const toast = document.createElement('div');
  toast.innerText = "You Receive a New Notification"; // fixed text
  toast.style.cssText = "background:#333;color:#fff;padding:10px 16px;border-radius:8px;margin-top:8px;box-shadow:0 2px 6px rgba(0,0,0,0.2);opacity:0;transition:opacity 0.5s, transform 0.5s;transform:translateY(-20px)";
  
  toastContainer.appendChild(toast);

  setTimeout(()=>{
    toast.style.opacity='1'; 
    toast.style.transform='translateY(0)';
  },100);

  setTimeout(()=>{
    toast.style.opacity='0'; 
    toast.style.transform='translateY(-20px)'; 
    setTimeout(()=>toast.remove(),500);
  },4000);
}

function showToast(msg) {
  const toast=document.createElement('div');
  toast.innerText=`🔔 ${msg}`;
  toast.style.cssText="background:#333;color:#fff;padding:10px 16px;border-radius:8px;margin-top:8px;box-shadow:0 2px 6px rgba(0,0,0,0.2);opacity:0;transition:opacity 0.5s, transform 0.5s;transform:translateY(-20px)";
  toastContainer.appendChild(toast);
  setTimeout(()=>{toast.style.opacity='1'; toast.style.transform='translateY(0)';},100);
  setTimeout(()=>{toast.style.opacity='0'; toast.style.transform='translateY(-20px)'; setTimeout(()=>toast.remove(),500);},4000);
}

async function fetchNotifications() {
  try {
    const res = await fetch(basePath + `get_notifications.php?page=${currentPage}&size=${pageSize}${showUnreadOnly ? '&unread_only=1' : ''}`);
    const data = await res.json();
    totalNotifications = data.totalCount;
    totalUnread = data.totalUnread;

    const tbody = document.querySelector('#notificationTable tbody');
    tbody.innerHTML = '';

    data.notifications.forEach(n => {
      const tr = document.createElement('tr');
      tr.style.cursor = 'pointer';
      if (!n.read) tr.style.fontWeight = 'bold';

      // Truncate message for sidebar (e.g., 50 chars)
      let displayMsg = n.message.length > 10 ? n.message.slice(0, 10) + '…' : n.message;

      // Status class for colored badge
      let statusClass = n.read ? 'Read' : 'New';

      tr.innerHTML = `
        <td title="${n.message}">${displayMsg}</td>
        <td>${n.date}</td>
        <td class="${statusClass}">${n.read ? 'Read' : 'New'}</td>
      `;

      tr.onclick = () => openNotificationModal(n);
      tbody.appendChild(tr);
    });

    const effectiveTotal = showUnreadOnly ? totalUnread : totalNotifications;
    btnPrev.disabled = currentPage <= 1;
    btnNext.disabled = (currentPage * pageSize) >= effectiveTotal;

    if (data.notifications.length > 0) {
      const newest = data.notifications[0];
      if (initialized && isLiveUpdate && newest.id > lastAlertedId) playNotificationEffect(newest.message);
      lastAlertedId = Math.max(lastAlertedId, newest.id);
    }

    if (!initialized) initialized = true;
    isLiveUpdate = false;
  } catch (e) {
    console.error(e);
  }
}

btnPrev.addEventListener('click',()=>{if(currentPage>1){currentPage--;fetchNotifications();}});
btnNext.addEventListener('click',()=>{const effectiveTotal = showUnreadOnly?totalUnread:totalNotifications;if((currentPage*pageSize)<effectiveTotal){currentPage++;fetchNotifications();}});

function openNotificationModal(notification) {
  // Show modal
  const modal = document.getElementById('notifModal');
  const header = document.getElementById('notifModalHeader');
  const message = document.getElementById('notifModalMessage');
  const footer = document.getElementById('notifModalFooter');

  // Set header based on status
  header.innerText = notification.status === 'Approved' ? 'Approved' : 'Rejected';
  message.innerText = notification.message;

  // Set footer date
  footer.innerText = notification.date;

  modal.style.display = 'flex';

  // Mark notification as read
  if (!notification.read) markAsRead(notification.id);
}

// Close modal
document.getElementById('closeNotifModal').onclick = () => {
  document.getElementById('notifModal').style.display = 'none';
};

// Also close modal if clicking outside content
document.getElementById('notifModal').addEventListener('click', (e) => {
  if(e.target === document.getElementById('notifModal')) {
    document.getElementById('notifModal').style.display = 'none';
  }
});


// Bell click: show sidebar
bell.addEventListener('click',()=>{
  currentPage=1;
  fetchNotifications();
  fetchUnreadCount();
  notifSidebar.classList.add('open');
});

// Close sidebar
closeNotifSidebar.onclick = () => { notifSidebar.classList.remove('open'); };

async function markAsRead(id){
  try{
    const res = await fetch(basePath+'mark_as_read.php',{
      method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`id=${id}`
    });
    const result = await res.json();
    if(result.status==='success'){fetchNotifications(); fetchUnreadCount();}
  }catch(e){console.error(e);}
}

// SSE Live update
let eventSource;
function initSSE(){
  if(eventSource) eventSource.close();
  eventSource=new EventSource(basePath+'notifications_stream.php');
  eventSource.onmessage=function(){isLiveUpdate=true; fetchUnreadCount(); fetchNotifications();}
  eventSource.onerror=function(){console.log('SSE lost, reconnecting...'); setTimeout(initSSE,3000);}
}

function initializeNotifications(){fetchUnreadCount(); fetchNotifications(); initSSE();}
initializeNotifications();

// Mark all as read
document.getElementById('markAllReadBtn').addEventListener('click', async ()=>{
  try{
    const res=await fetch(basePath+'mark_as_read.php',{method:'POST'});
    const r=await res.json();
    if(r.status==='success'){fetchNotifications(); fetchUnreadCount();}
  }catch(e){console.error(e);}
});

// Filter unread / show all
document.getElementById('filterUnreadBtn').addEventListener('click',()=>{
  showUnreadOnly=true;
  document.getElementById('showAllBtn').style.display='inline-block';
  document.getElementById('filterUnreadBtn').style.display='none';
  currentPage=1; fetchNotifications();
});
document.getElementById('showAllBtn').addEventListener('click',()=>{
  showUnreadOnly=false;
  document.getElementById('showAllBtn').style.display='none';
  document.getElementById('filterUnreadBtn').style.display='inline-block';
  currentPage=1; fetchNotifications();
});
</script>
