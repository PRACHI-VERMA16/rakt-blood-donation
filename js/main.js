// Load emergency requests from PHP
async function loadEmergencyRequests() {
  const res = await fetch('../php/get_emergency.php');
  const data = await res.json();
  const container = document.getElementById('emergency-list');
  container.innerHTML = '';
  data.forEach(req => {
    const badge = req.urgency === 'Critical' ? 'badge-critical' :
                  req.urgency === 'Urgent' ? 'badge-urgent' : 'badge-moderate';
    container.innerHTML += `
      <div class="request-card">
        <div style="display:flex;justify-content:space-between">
          <strong style="color:#C0392B">${req.blood_type} · ${req.units_needed} Unit(s)</strong>
          <span class="${badge}">${req.urgency}</span>
        </div>
        <p>${req.hospital}</p>
        <p>📍 ${req.city}</p>
        <p>📞 ${req.contact_phone}</p>
        <button onclick="contactHospital('${req.contact_phone}')" 
          style="width:100%;margin-top:10px;padding:10px;background:#8B1A1A;color:white;border:none;border-radius:6px;cursor:pointer">
          Contact Hospital
        </button>
      </div>`;
  });
}

// Register donor
async function registerDonor() {
  const payload = {
    full_name: document.getElementById('fullName').value,
    email: document.getElementById('email').value,
    phone: document.getElementById('phone').value,
    age: document.getElementById('age').value,
    blood_type: document.getElementById('bloodType').value,
    city: document.getElementById('city').value
  };

  if (!payload.full_name || !payload.email || !payload.blood_type) {
    alert('Please fill all required fields!');
    return;
  }

  const res = await fetch('../php/register_donor.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  alert(data.success ? '✅ Registered successfully!' : '❌ Error: ' + data.error);
}

// Blood compatibility data
const compatibility = {
  'A+':  { donate: ['A+','AB+'], receive: ['A+','A-','O+','O-'], population: '35.7%' },
  'A-':  { donate: ['A+','A-','AB+','AB-'], receive: ['A-','O-'], population: '6.3%' },
  'B+':  { donate: ['B+','AB+'], receive: ['B+','B-','O+','O-'], population: '8.5%' },
  'B-':  { donate: ['B+','B-','AB+','AB-'], receive: ['B-','O-'], population: '1.5%' },
  'O+':  { donate: ['O+','A+','B+','AB+'], receive: ['O+','O-'], population: '37.4%' },
  'O-':  { donate: ['All Types'], receive: ['O-'], population: '6.6%' },
  'AB+': { donate: ['AB+'], receive: ['All Types'], population: '3.4%' },
  'AB-': { donate: ['AB+','AB-'], receive: ['AB-','A-','B-','O-'], population: '0.6%' }
};

function findCompatibility() {
  const bt = document.getElementById('bloodTypeSelect').value;
  if (!compatibility[bt]) return;
  const c = compatibility[bt];
  document.getElementById('compat-result').innerHTML = `
    <div class="compat-card">
      <h3>${bt} — ${c.population} of population</h3>
      <p>Can donate to: ${c.donate.join(', ')}</p>
      <p>Can receive from: ${c.receive.join(', ')}</p>
    </div>`;
}

function contactHospital(phone) {
  window.location.href = `tel:${phone}`;
}

// Init
loadEmergencyRequests();