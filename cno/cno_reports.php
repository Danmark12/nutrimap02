<?php
session_start();
require '../db/config.php';

// Only allow CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

// Fetch reports
$statuses = ['Pending', 'Approved', 'Rejected'];
$reports = [];
foreach ($statuses as $status) {
    $stmt = $pdo->prepare("
        SELECT r.id, b.title, u.first_name, u.last_name, u.barangay, r.status, r.report_time, r.report_date
        FROM reports r
        JOIN bns_reports b ON b.report_id = r.id
        JOIN users u ON r.user_id = u.id
        WHERE r.status=? AND r.is_submitted=1
        ORDER BY r.report_date DESC, r.report_time DESC
    ");
    $stmt->execute([$status]);
    $reports[$status] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch barangays for filter
$barangays = $pdo->query("SELECT DISTINCT barangay FROM users ORDER BY barangay ASC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CNO | Reports</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main class="flex-1 p-6">
<div class="container mx-auto space-y-6">

<h1 class="text-2xl font-bold text-gray-700">Reports</h1>

<!-- Tabs -->
<div class="flex border-b border-gray-300">
    <button class="tab-button py-2 px-4 text-teal-600 font-semibold border-b-2 border-teal-600" data-tab="Pending">Pending</button>
    <button class="tab-button py-2 px-4 text-gray-500 font-semibold" data-tab="Approved">Approved</button>
    <button class="tab-button py-2 px-4 text-gray-500 font-semibold" data-tab="Rejected">Rejected</button>
</div>

<!-- Filters -->
<div class="flex gap-4 mt-4">
    <select id="barangayFilter" class="px-3 py-2 border border-gray-300 rounded shadow-sm">
        <option value="">All Barangays</option>
        <?php foreach($barangays as $b): ?>
        <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
        <?php endforeach; ?>
    </select>
    <select id="sortFilter" class="px-3 py-2 border border-gray-300 rounded shadow-sm">
        <option value="">Sort By</option>
        <option value="name-asc">Name A-Z</option>
        <option value="name-desc">Name Z-A</option>
        <option value="time-new">Time New-Old</option>
        <option value="time-old">Time Old-New</option>
    </select>
</div>

<!-- Tables -->
<?php foreach ($statuses as $status): ?>
<div id="<?= $status ?>-table" class="tab-content mt-4 <?= $status !== 'Pending' ? 'hidden' : '' ?>">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full table-auto">
            <thead class="bg-teal-600 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Full Name</th>
                    <th class="px-4 py-2 text-left">Barangay</th>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Time</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <?php if($status==='Pending'): ?><th class="px-4 py-2 text-left">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody id="<?= $status ?>-reports-body" class="text-gray-700">
            <?php foreach($reports[$status] as $r): ?>
            <tr data-id="<?= $r['id'] ?>" data-barangay="<?= $r['barangay'] ?>" data-timestamp="<?= $r['report_date'].' '.$r['report_time'] ?>" class="border-b hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['barangay']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['title']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['report_date']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['report_time']) ?></td>
                <td class="px-4 py-2 status"><?= ucfirst($r['status']) ?></td>
                <?php if($status==='Pending'): ?>
                <td class="px-4 py-2 flex gap-2">
                    <button class="approve-button bg-green-100 text-green-700 px-2 py-1 rounded">Approve</button>
                    <button class="decline-button bg-red-100 text-red-700 px-2 py-1 rounded">Decline</button>
                    <a href="view_report.php?id=<?= $r['id'] ?>" class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">View</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <!-- Pagination inside table -->
            <tfoot>
                <tr>
                    <td colspan="<?= $status==='Pending'?7:6 ?>" class="px-4 py-2">
                        <div class="flex justify-center space-x-2" id="<?= $status ?>-pagination"></div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endforeach; ?>

<div id="message-box" class="hidden p-2 rounded font-semibold"></div>
</div>
</main>

<script>
// Tabs
const tabs = document.querySelectorAll('.tab-button');
const contents = document.querySelectorAll('.tab-content');
tabs.forEach(tab=>{
    tab.addEventListener('click',()=>{
        tabs.forEach(t=>{t.classList.remove('text-teal-600','border-teal-600'); t.classList.add('text-gray-500');});
        tab.classList.add('text-teal-600','border-teal-600');
        contents.forEach(c=>c.classList.add('hidden'));
        document.getElementById(tab.dataset.tab+'-table').classList.remove('hidden');
        applyFilters();
    });
});

// Show message
const messageBox = document.getElementById('message-box');
function showMessage(msg, success=true){
    messageBox.textContent = msg;
    messageBox.classList.remove('hidden','bg-green-600','bg-red-600');
    messageBox.classList.add(success ? 'bg-green-600' : 'bg-red-600','text-white');
    setTimeout(()=>messageBox.classList.add('hidden'),3000);
}

// Approve/Decline actions
document.querySelector('#Pending-reports-body').addEventListener('click', function(e){
    const btn = e.target.closest('button');
    if(!btn) return;
    const row = btn.closest('tr');
    if(!row) return;

    const reportId = row.dataset.id;
    if(!reportId) return;

    const action = btn.classList.contains('approve-button') ? 'approve' :
                   btn.classList.contains('decline-button') ? 'reject' : null;
    if(!action) return;

    let message = '';
    if(action === 'reject') {
        message = prompt("Enter a message for declining this report:");
        if(message === null) return; // user cancelled
    }

    fetch('update_status.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`report_id=${reportId}&action=${action}&message=${encodeURIComponent(message)}`
    })
    .then(r=>r.json())
    .then(data=>{
        if(data.error){ showMessage(data.error,false); return; }

        const status = (data.status||'').toLowerCase();
        const tbody = document.getElementById(status==='approved'?'Approved-reports-body':'Rejected-reports-body');

        row.querySelector('.status').textContent = status.charAt(0).toUpperCase() + status.slice(1);

        // Only remove actions if it was pending
        const actionsTd = row.querySelector('td:last-child');
        if(actionsTd) actionsTd.remove();

        // Add a new message cell if rejected
        if(status === 'rejected') {
            const msgCell = document.createElement('td');
            msgCell.textContent = data.message || '';
            row.appendChild(msgCell);
        }

        tbody.appendChild(row);
        showMessage(`Report ${status}`, status==='approved');
        applyFilters();
    })
    .catch(()=>showMessage('Network error', false));
});

// Filters & Sorting
const barangayFilter = document.getElementById('barangayFilter');
const sortFilter = document.getElementById('sortFilter');
[barangayFilter,sortFilter].forEach(el=>el.addEventListener('change',applyFilters));

function applyFilters(){
    ['Pending','Approved','Rejected'].forEach(status=>{
        const tbody=document.getElementById(status+'-reports-body');
        const rows=Array.from(tbody.querySelectorAll('tr'));
        const visible=rows.filter(r=>{
            return !barangayFilter.value || r.dataset.barangay===barangayFilter.value;
        });
        // Sorting
        const sortOption=sortFilter.value;
        if(sortOption){
            visible.sort((a,b)=>{
                if(sortOption.startsWith('name')){
                    const nameA=a.cells[0].textContent.toLowerCase();
                    const nameB=b.cells[0].textContent.toLowerCase();
                    return sortOption==='name-asc'?nameA.localeCompare(nameB):nameB.localeCompare(nameA);
                } else if(sortOption.startsWith('time')){
                    const timeA=new Date(a.dataset.timestamp).getTime();
                    const timeB=new Date(b.dataset.timestamp).getTime();
                    return sortOption==='time-new'?timeB-timeA:timeA-timeB;
                }
            });
        }
        visible.forEach(r=>tbody.appendChild(r));
        rows.forEach(r=>r.style.display=visible.includes(r)?'':'none');
        paginateTable(tbody,status,visible);
    });
}

// Pagination inside table
const perPage=5;
function paginateTable(tbody,status,rows){
    const pageContainer=document.getElementById(status+'-pagination');
    pageContainer.innerHTML='';
    const totalPages=Math.ceil(rows.length/perPage);
    if(totalPages<=1) return;
    let currentPage=1;

    function renderPage(page){
        currentPage=page;
        rows.forEach((r,i)=>r.style.display=(i>=perPage*(page-1)&&i<perPage*page)?'':'none');
        pageContainer.innerHTML='';
        for(let i=1;i<=totalPages;i++){
            const btn=document.createElement('button');
            btn.textContent=i;
            btn.className='px-2 py-1 border rounded '+(i===page?'bg-teal-600 text-white':'bg-gray-100 text-gray-700');
            btn.addEventListener('click',()=>renderPage(i));
            pageContainer.appendChild(btn);
        }
    }
    renderPage(1);
}

// Initial filter & pagination
applyFilters();
</script>
</body>
</html>
