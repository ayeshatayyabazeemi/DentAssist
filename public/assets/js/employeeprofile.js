const editBtn = document.getElementById('editBtn');
const editModal = document.getElementById('editModal');
const closeEdit = document.getElementById('closeEdit');
const editForm = document.getElementById('editForm');
const deleteBtn = document.getElementById('deleteBtn');

// Function to show notification at top-right
function showNotification(message, type='success'){
    // Remove old notification if exists
    const existing = document.querySelector('.notification-box');
    if(existing) existing.remove();

    const div = document.createElement('div');
    div.classList.add('notification-box');
    div.textContent = message;

    // Inline styles
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

    // Trigger fade-in
    setTimeout(()=>{
        div.style.opacity = '1';
        div.style.transform = 'translateY(0)';
    }, 50);

    // Fade out after 4 seconds
    setTimeout(()=>{
        div.style.opacity = '0';
        div.style.transform = 'translateY(-20px)';
        setTimeout(()=> div.remove(), 500);
    }, 4000);
}

// Open / Close Edit Modal
editBtn.addEventListener('click', ()=>{ 
    editModal.classList.add('show'); 
    editModal.setAttribute('aria-hidden','false'); 
});
closeEdit.addEventListener('click', ()=>{ 
    editModal.classList.remove('show'); 
    editModal.setAttribute('aria-hidden','true'); 
});

// Delete Employee
deleteBtn.addEventListener('click', ()=>{
    if(!confirm('Are you sure you want to delete this employee?')) return;
    const empId = deleteBtn.dataset.id;
    fetch('/api/employee/' + empId, { method:'DELETE' })
        .then(res => res.json())
        .then(r => { 
            showNotification(r.message, r.status==='success' ? 'success' : 'error');
            if(r.status==='success') setTimeout(()=> location.href='/adminDashboard', 1000);
        });
});

// Handle Edit Form Submit
editForm.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(editForm);
    const obj = {};
    formData.forEach((v,k)=>{
        if(k.includes('schedule')){
            const match = k.match(/schedule\[(\d+)\]\[(\w+)\]/);
            if(match){
                obj['schedule'] = obj['schedule']||[];
                const idx = parseInt(match[1]);
                obj['schedule'][idx] = obj['schedule'][idx]||{};
                obj['schedule'][idx][match[2]]=v;
            }
        } else obj[k]=v;
    });

    const empId = deleteBtn.dataset.id;
    fetch('/api/employee/update/' + empId,{
        method:'PUT',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify(obj)
    }).then(res=>res.json())
      .then(r=>{
          showNotification(r.message, r.status==='success' ? 'success' : 'error');
          if(r.status==='success') setTimeout(()=> location.reload(), 1000);
      });
});
