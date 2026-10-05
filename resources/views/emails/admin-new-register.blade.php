@component('mail::message')
# New Registration Received

A new registration has been submitted on **MSME Direct**.

---

### 📌 Basic Details
- **Organization:** {{ $data['organization_name'] }}
- **Email:** {{ $data['email'] }}
- **Contact No:** {{ $data['contact_no'] }}
- **City / State:** {{ $data['city'] }}, {{ $data['state'] }}

---

### 🏢 Business Details
- **MSME Type:** {{ $data['msme_type'] }}
- **Business Category:** {{ $data['business_category'] }}
- **Experience:** {{ $data['experience'] }} years
- **Specialization:** {{ $data['specialization'] }}

---

@component('mail::button', ['url' => $data['admin_url']])
View in Admin Panel
@endcomponent

Thanks,  
**MSME Direct System**
@endcomponent
