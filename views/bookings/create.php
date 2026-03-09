<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<nav class="mb-6">
<ol class="flex items-center space-x-2 text-sm text-gray-500">
<li><a href="<?= url('/') ?>" class="hover:text-ph-blue">Home</a></li>
<li><i class="fas fa-chevron-right text-xs"></i></li>
<li><a href="<?= url('courts/' . $court['id']) ?>" class="hover:text-ph-blue"><?= $court['name'] ?></a></li>
<li><i class="fas fa-chevron-right text-xs"></i></li>
<li class="text-gray-900">Book</li>
</ol>
</nav>

<h1 class="text-2xl font-bold text-gray-900 mb-6">Book <?= $court['name'] ?></h1>

<div class="grid md:grid-cols-3 gap-8">

<!-- BOOKING FORM -->
<div class="md:col-span-2">

<form action="<?= url('bookings') ?>" method="POST" id="booking-form" class="bg-white rounded-xl shadow-sm p-6 space-y-6">

<input type="hidden" name="_token" value="<?= csrf_token() ?>">
<input type="hidden" name="court_id" value="<?= $court['id'] ?>">

<!-- DATE -->
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>

<input type="date"
name="booking_date"
id="booking-date"
value="<?= $selectedDate ?>"
min="<?= date('Y-m-d') ?>"
max="<?= date('Y-m-d', strtotime('+' . BOOKING_ADVANCE_DAYS . ' days')) ?>"
class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-ph-blue"
required>
</div>


<!-- TIME SLOTS -->
<div>

<label class="block text-sm font-medium text-gray-700 mb-2">Select Time Slot</label>

<p class="text-sm text-gray-500 mb-3">Click start time then end time</p>

<div id="time-slots" class="grid grid-cols-4 md:grid-cols-6 gap-2 mb-4">

<?php foreach ($slots as $slot): ?>

<button
type="button"
class="time-slot-btn py-3 px-2 rounded-lg text-center text-sm font-medium transition
<?= $slot['available']
? ($slot['is_peak']
? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-800 border-2 border-transparent'
: 'bg-green-100 hover:bg-green-200 text-green-800 border-2 border-transparent')
: 'bg-gray-100 text-gray-400 cursor-not-allowed' ?>"
data-time="<?= $slot['start'] ?>"
<?= !$slot['available'] ? 'disabled' : '' ?>
>

<?= date('g:i A', strtotime($slot['start'])) ?>

<?php if ($slot['is_peak']): ?>
<span class="block text-xs">Peak</span>
<?php endif; ?>

</button>

<?php endforeach; ?>

</div>

<input type="hidden" name="start_time" id="start-time" required>
<input type="hidden" name="end_time" id="end-time" required>

</div>


<!-- HALF COURT -->
<?php if (($court['court_type_slug'] ?? '') === 'basketball' && !empty($court['half_court_rate'])): ?>

<div class="flex items-center">
<input type="checkbox"
name="is_half_court"
id="half-court"
value="1"
class="h-5 w-5 text-ph-blue border-gray-300 rounded">

<label for="half-court" class="ml-3 text-gray-700">
Half Court Only (<?= formatPrice($court['half_court_rate']) ?>/hr)
</label>

</div>

<?php endif; ?>


<!-- PLAYERS -->
<div>

<label class="block text-sm font-medium text-gray-700 mb-2">
Number of Players (Optional)
</label>

<input
type="number"
name="player_count"
min="1"
max="<?= $court['capacity'] ?? 30 ?>"
class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-ph-blue"
placeholder="How many players?">

</div>


<!-- NOTES -->
<div>

<label class="block text-sm font-medium text-gray-700 mb-2">
Special Requests
</label>

<textarea
name="notes"
rows="3"
class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-ph-blue"
placeholder="Any requests..."></textarea>

</div>


<!-- PAYMENT -->
<div>

<label class="block text-sm font-medium text-gray-700 mb-2">
Payment Option
</label>

<div class="grid grid-cols-2 gap-4">

<label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer">

<input type="radio" name="payment_type" value="online" checked class="sr-only peer">

<div class="text-center peer-checked:text-ph-blue">

<i class="fas fa-qrcode text-2xl mb-2"></i>

<p class="font-medium">Pay Now (QR)</p>

<p class="text-xs text-gray-500">GCash, Maya, Bank</p>

</div>

</label>


<label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer">

<input type="radio" name="payment_type" value="venue" class="sr-only peer">

<div class="text-center peer-checked:text-ph-blue">

<i class="fas fa-store text-2xl mb-2"></i>

<p class="font-medium">Pay at Venue</p>

<p class="text-xs text-gray-500">Expires in <?= RESERVATION_EXPIRY_MINUTES ?> mins</p>

</div>

</label>

</div>

</div>


<button
type="submit"
id="submit-btn"
disabled
class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 disabled:bg-gray-300">

<i class="fas fa-calendar-check mr-2"></i>
Confirm Booking

</button>

</form>

</div>


<!-- SUMMARY -->
<div class="md:col-span-1">

<div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">

<h3 class="font-semibold text-gray-900 mb-4">Booking Summary</h3>

<div id="price-summary" class="hidden space-y-3 text-sm">

<div class="flex justify-between">
<span>Date</span>
<span id="summary-date"></span>
</div>

<div class="flex justify-between">
<span>Time</span>
<span id="summary-time"></span>
</div>

<div class="flex justify-between">
<span>Duration</span>
<span id="summary-duration"></span>
</div>

<hr>

<div class="flex justify-between text-lg font-bold">
<span>Total</span>
<span id="summary-total" class="text-ph-blue"></span>
</div>

<div class="bg-yellow-50 p-3 rounded">

Downpayment:
<span id="summary-downpayment"></span>

</div>

</div>

<div id="no-selection" class="text-center text-gray-500 py-4">

<i class="fas fa-clock text-3xl mb-2"></i>

<p>Select time slots to see price</p>

</div>

</div>

</div>

</div>
</div>


<script>

let startTime = null;
let endTime = null;

const submitBtn = document.getElementById("submit-btn");


// SLOT CLICK

document.querySelectorAll(".time-slot-btn:not([disabled])").forEach(btn=>{
btn.addEventListener("click",handleSlotClick);
});


function handleSlotClick(){

const time = this.dataset.time;

if(!startTime || endTime){

startTime = time;
endTime = null;
clearSelection();
this.classList.add("border-ph-blue","bg-ph-blue","text-white");

}
else{

if(time <= startTime){

startTime = time;
clearSelection();
this.classList.add("border-ph-blue","bg-ph-blue","text-white");

}
else{

endTime = time;

const [h,m] = endTime.split(":");
endTime = `${String(parseInt(h)+1).padStart(2,"0")}:${m}`;

highlightRange();
updatePriceSummary();

}

}

}


function clearSelection(){

document.querySelectorAll(".time-slot-btn").forEach(btn=>{
btn.classList.remove("border-ph-blue","bg-ph-blue","text-white");
});

submitBtn.disabled = true;

}


function highlightRange(){

document.querySelectorAll(".time-slot-btn").forEach(btn=>{
const time = btn.dataset.time;

if(time >= startTime && time < endTime){

btn.classList.add("border-ph-blue","bg-ph-blue","text-white");

}

});

submitBtn.disabled = false;

}


// PRICE API

function updatePriceSummary(){

const date = document.getElementById("booking-date").value;

const halfCourtBox = document.getElementById("half-court");
const isHalfCourt = halfCourtBox && halfCourtBox.checked ? "1" : "0";

fetch(`<?= url('api/courts/' . $court['id'] . '/price') ?>?date=${date}&start_time=${startTime}&end_time=${endTime}&half_court=${isHalfCourt}`)

.then(r=>r.json())

.then(data=>{

if(!data.success) return;

document.getElementById("start-time").value=startTime;
document.getElementById("end-time").value=endTime;

document.getElementById("summary-date").textContent=formatDate(date);

document.getElementById("summary-time").textContent=`${formatTime(startTime)} - ${formatTime(endTime)}`;

document.getElementById("summary-duration").textContent=`${data.price.hours} hour(s)`;

document.getElementById("summary-total").textContent=data.formatted.total;

document.getElementById("summary-downpayment").textContent=data.formatted.downpayment;

document.getElementById("price-summary").classList.remove("hidden");
document.getElementById("no-selection").classList.add("hidden");

});

}


// FORM VALIDATION

document.getElementById("booking-form").addEventListener("submit",function(e){

if(!startTime || !endTime){

e.preventDefault();
alert("Please select time slot first.");

}

});


// FORMATTERS

function formatDate(d){

const date = new Date(d);

return date.toLocaleDateString("en-PH",{
weekday:"short",
month:"short",
day:"numeric",
year:"numeric"
});

}

function formatTime(t){

const h=parseInt(t.split(":")[0]);

const ampm=h>=12?"PM":"AM";

const hour=h%12||12;

return `${hour}:00 ${ampm}`;

}

</script>