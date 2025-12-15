<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Hotel_Reservation_System/app/public/index.php?controller=login&action=index&error=unauthorized");
    exit;
}
?>

<link rel="stylesheet" href="./css/dashboard.style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" href="../public/assets/Lunera-Logo.png" type="image/ico">

<style>
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    justify-content: center;
    align-items: center;
    z-index: 999;
}
.modal-content {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    width: 420px;
}
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
.btn-confirm { background:#28a745;color:#fff;border:none;padding:8px 14px;cursor:pointer; }
.btn-cancel { background:#ccc;border:none;padding:8px 14px;cursor:pointer; }

@media print {
    body * { visibility: hidden; }
    #receiptArea, #receiptArea * { visibility: visible; }
    #receiptArea { position:absolute; top:0; left:0; width:100%; }
    .no-print { display:none; }
}
</style>

<body>

<div class="sidebar">
    <div>
        <h2><i class="fa-solid fa-hotel"></i> Admin Panel</h2>
    </div>
</div>

<div class="main">
    <h1>Current Guests</h1>

    <input type="text" id="guestSearch" placeholder="Search..." style="width:300px;padding:8px;margin-bottom:15px;">

    <table id="guestsTable">
        <thead>
            <tr>
                <th>Guest Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Room Type</th>
                <th>Room No.</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($currentGuests as $guest): ?>
            <tr>
                <td class="g-name"><?= htmlspecialchars($guest['GuestName']) ?></td>
                <td class="g-email"><?= htmlspecialchars($guest['Email']) ?></td>
                <td class="g-contact"><?= htmlspecialchars($guest['Contact']) ?></td>
                <td class="g-room-type"><?= htmlspecialchars($guest['RoomType']) ?></td>
                <td class="g-room-no"><?= htmlspecialchars($guest['RoomNumber']) ?></td>
                <td class="g-checkin"><?= $guest['CheckIn'] ?></td>
                <td class="g-checkout"><?= $guest['CheckOut'] ?></td>
                <td>
                    <form method="POST"
                        action="/Hotel_Reservation_System/app/public/index.php?controller=admin&action=checkoutGuest"
                        class="checkout-form">
                        <input type="hidden" name="guest_id" value="<?= $guest['GuestID'] ?>">
                        <input type="hidden" name="booking_id" value="<?= $guest['BookingID'] ?>">
                        <input type="hidden" name="room_id" value="<?= $guest['RoomID'] ?>">
                        <button type="button" class="btn-delete checkout-btn">Check-out</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal" id="checkoutModal">
    <div class="modal-content" id="receiptArea">
        <h3 style="text-align:center;">Hotel Checkout Receipt</h3>
        <hr>

        <p><strong>Guest:</strong> <span id="mName"></span></p>
        <p><strong>Email:</strong> <span id="mEmail"></span></p>
        <p><strong>Contact:</strong> <span id="mContact"></span></p>
        <p><strong>Room:</strong> <span id="mRoom"></span></p>
        <p><strong>Check-in:</strong> <span id="mCheckin"></span></p>
        <p><strong>Check-out:</strong> <span id="mCheckout"></span></p>

        <hr>
        <p style="text-align:center;font-size:12px;">Thank you for staying with us!</p>

        <div class="modal-actions no-print">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-confirm" onclick="printReceipt()">Print Receipt</button>
            <button class="btn-confirm" id="confirmCheckout">Confirm Checkout</button>
        </div>
    </div>
</div>

<script>
let activeForm = null;

document.getElementById('guestSearch').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll('#guestsTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
    });
});

document.querySelectorAll('.checkout-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const row = this.closest('tr');
        activeForm = this.closest('form');

        document.getElementById('mName').textContent = row.querySelector('.g-name').textContent;
        document.getElementById('mEmail').textContent = row.querySelector('.g-email').textContent;
        document.getElementById('mContact').textContent = row.querySelector('.g-contact').textContent;
        document.getElementById('mRoom').textContent =
            row.querySelector('.g-room-type').textContent + ' #' +
            row.querySelector('.g-room-no').textContent;
        document.getElementById('mCheckin').textContent = row.querySelector('.g-checkin').textContent;
        document.getElementById('mCheckout').textContent = row.querySelector('.g-checkout').textContent;

        document.getElementById('checkoutModal').style.display = 'flex';
    });
});

document.getElementById('confirmCheckout').addEventListener('click', function () {
    if (activeForm) activeForm.submit();
});

function closeModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}

function printReceipt() {
    window.print();
}
</script>

</body>
