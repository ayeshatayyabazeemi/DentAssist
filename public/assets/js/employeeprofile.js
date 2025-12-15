// employeeprofile.js

const editBtn = document.getElementById('editBtn');
const editModal = document.getElementById('editModal');
const closeEdit = document.getElementById('closeEdit');
const editForm = document.getElementById('editForm');
const deleteBtn = document.getElementById('deleteBtn');

// Notification function
function showNotification(message, type='success'){
    const existing = document.querySelector('.notification-box');
    if(existing) existing.remove();
    const div = document.createElement('div');
    div.classList.add('notification-box');
    div.textContent = message;
    div.style.position = 'fixed';
    div.style.top = '20px';
    div.style.right = '20px';
    div.style.padding = '10px 16px';
    div.style.borderRadius = '5px';
    div.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
    div.style.color = '#fff';
    div.style.fontWeight = '500';
    div.style.zIndex = '2000';
    div.style.whiteSpace = 'nowrap';
    div.style.opacity = '0';
    div.style.transition = 'opacity 0.5s, transform 0.5s';
    div.style.transform = 'translateY(-20px)';
    document.body.appendChild(div);
    setTimeout(()=>{ div.style.opacity = '1'; div.style.transform = 'translateY(0)'; },50);
    setTimeout(()=>{ div.style.opacity = '0'; div.style.transform = 'translateY(-20px)'; setTimeout(()=> div.remove(),500); },4000);
}

// Open/Close modal
editBtn.addEventListener('click', ()=>{ 
    editModal.classList.add('show'); 
    editModal.setAttribute('aria-hidden','false'); 
});
closeEdit.addEventListener('click', ()=>{ 
    editModal.classList.remove('show'); 
    editModal.setAttribute('aria-hidden','true'); 
});

// Delete employee
deleteBtn.addEventListener('click', ()=>{
    if(!confirm('Are you sure you want to delete this employee?')) return;
    const empId = deleteBtn.dataset.id;
    fetch('/api/employee/' + empId, { method:'DELETE' })
        .then(res => res.json())
        .then(r => { 
            showNotification(r.message, r.status==='success' ? 'success' : 'error');
            if(r.status==='success') setTimeout(()=> location.href='/adminDashboard',1000);
        });
});

// Field error handling
function setFieldError(input,message){
    const oldErr = input.nextElementSibling;
    if(oldErr && oldErr.classList.contains('error-text')) oldErr.remove();
    const small = document.createElement('small');
    small.classList.add('error-text');
    small.textContent = message;
    input.insertAdjacentElement('afterend', small);
}
function clearFieldError(input){
    const oldErr = input.nextElementSibling;
    if(oldErr && oldErr.classList.contains('error-text')) oldErr.remove();
}

// Handle edit form submission
editForm.addEventListener('submit', function(e){
    e.preventDefault();
    
    const phoneInput = editForm.querySelector('input[name="mobile_no"]');
    const cnicInput  = editForm.querySelector('input[name="cnic"]');
    let hasError = false;

    // Clear previous errors
    clearFieldError(phoneInput);
    clearFieldError(cnicInput);

    // Validate phone (mandatory)
    if(!/^\d{11}$/.test(phoneInput.value.trim())){
        setFieldError(phoneInput,'Phone number must be exactly 11 digits');
        hasError = true;
    }

    // Validate CNIC (optional)
    const cnicValue = cnicInput.value.trim();
    if(cnicValue && !/^\d{13}$/.test(cnicValue)){
        setFieldError(cnicInput,'CNIC must be exactly 13 digits');
        hasError = true;
    }

    if(hasError) return;

    // Collect form data
    const formData = new FormData(editForm);
    const obj = {};
    formData.forEach((v,k)=>{
        if(k.includes('schedule')){
            const match = k.match(/schedule\[(\d+)\]\[(\w+)\]/);
            if(match){
                obj['schedule'] = obj['schedule']||[];
                const idx = parseInt(match[1]);
                obj['schedule'][idx] = obj['schedule'][idx]||{};
                obj['schedule'][idx][match[2]] = v;
            }
        } else {
            obj[k] = v;
        }
    });

    const empId = deleteBtn.dataset.id;

    fetch('/api/employee/update/' + empId,{
        method:'PUT',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify(obj)
    }).then(res=>res.json())
      .then(r=>{
          showNotification(r.message,r.status==='success'?'success':'error');
          if(r.status==='success') setTimeout(()=> location.reload(),1000);
      });
});
