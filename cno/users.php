<?php
session_start();
require "../db/config.php";

// ✅ Allow only CNO users
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

// ✅ Handle Activate/Deactivate actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'deactivate' ? 'Inactive' : 'Active';
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$action, $id]);
    header("Location: users.php");
    exit();
}

// Fetch Active Users
$stmt = $pdo->prepare("
    SELECT id, CONCAT(first_name,' ',last_name) AS full_name, email, barangay, user_type,
    DATE_FORMAT(created_at,'%m/%d/%Y') AS created_date
    FROM users WHERE status = 'Active' ORDER BY created_at DESC
");
$stmt->execute();
$activeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Inactive Users
$stmt = $pdo->prepare("
    SELECT id, CONCAT(first_name,' ',last_name) AS full_name, email, barangay, user_type,
    DATE_FORMAT(created_at,'%m/%d/%Y') AS created_date
    FROM users WHERE status = 'Inactive' ORDER BY created_at DESC
");
$stmt->execute();
$inactiveUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | User Management</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="flex-1 p-6 space-y-6">

  <!-- Tabs -->
  <div class="flex border-b border-gray-300">
    <button class="tab-button py-2 px-4 text-teal-600 font-semibold border-b-2 border-teal-600" data-tab="active">Active Users</button>
    <button class="tab-button py-2 px-4 text-gray-500 font-semibold" data-tab="inactive">Inactive Users</button>
  </div>

  <!-- Search bar -->
  <div class="w-full max-w-md mt-4">
    <input type="text" id="tableSearch" placeholder="Search users..." class="w-full px-4 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring focus:ring-teal-300">
  </div>

  <!-- Active Users -->
  <div id="active" class="tab-content mt-4">
    <div class="bg-white shadow rounded-lg">
      <div class="flex justify-between items-center px-4 py-2 bg-gray-100 rounded-t-lg">
        <span class="font-semibold text-gray-700">Active Users</span>
        <a href="add_user.php" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1 rounded">Add User</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-teal-600 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Name</th>
              <th class="px-4 py-2 text-left">Email</th>
              <th class="px-4 py-2 text-left">Barangay</th>
              <th class="px-4 py-2 text-left">Role</th>
              <th class="px-4 py-2 text-left">Created</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody id="userTable" class="text-gray-700">
            <?php if($activeUsers): foreach($activeUsers as $u): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($u['full_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['email']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['barangay']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['user_type']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['created_date']) ?></td>
              <td class="px-4 py-2 space-x-2">
                <a href="view_profile.php?id=<?= $u['id'] ?>" class="text-blue-600 hover:underline">View</a>
                <a href="users.php?action=deactivate&id=<?= $u['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Deactivate this user?')">Deactivate</a>
              </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center py-4 text-gray-500">No active users found</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Inactive Users -->
  <div id="inactive" class="tab-content mt-4 hidden">
    <div class="bg-white shadow rounded-lg">
      <div class="flex justify-between items-center px-4 py-2 bg-gray-100 rounded-t-lg">
        <span class="font-semibold text-gray-700">Inactive Users</span>
        <a href="add_user.php" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1 rounded">Add User</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-teal-600 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Name</th>
              <th class="px-4 py-2 text-left">Email</th>
              <th class="px-4 py-2 text-left">Barangay</th>
              <th class="px-4 py-2 text-left">Role</th>
              <th class="px-4 py-2 text-left">Created</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <?php if($inactiveUsers): foreach($inactiveUsers as $u): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($u['full_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['email']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['barangay']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['user_type']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($u['created_date']) ?></td>
              <td class="px-4 py-2 space-x-2">
                <a href="view_profile.php?id=<?= $u['id'] ?>" class="text-blue-600 hover:underline">View</a>
                <a href="users.php?action=activate&id=<?= $u['id'] ?>" class="text-green-600 hover:underline" onclick="return confirm('Activate this user?')">Activate</a>
              </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center py-4 text-gray-500">No inactive users found</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script>
// Tab switch
const tabs = document.querySelectorAll(".tab-button");
const contents = document.querySelectorAll(".tab-content");
tabs.forEach(tab => {
  tab.addEventListener("click", () => {
    tabs.forEach(t => t.classList.remove("text-teal-600","border-teal-600"));
    tabs.forEach(t => t.classList.add("text-gray-500"));
    tab.classList.add("text-teal-600","border-teal-600");

    contents.forEach(c => c.classList.add("hidden"));
    document.getElementById(tab.dataset.tab).classList.remove("hidden");

    // Clear search input when switching tabs
    document.getElementById("tableSearch").value = "";
    filterTable("");
  });
});

// Search filter
document.getElementById("tableSearch").addEventListener("keyup", function() {
  filterTable(this.value.toLowerCase());
});

function filterTable(search) {
  const activeRows = document.querySelectorAll("#active tbody tr");
  const inactiveRows = document.querySelectorAll("#inactive tbody tr");
  const tab = document.querySelector(".tab-button.text-teal-600").dataset.tab;
  const rows = tab === "active" ? activeRows : inactiveRows;
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(search) ? "" : "none";
  });
}
</script>

</body>
</html>
