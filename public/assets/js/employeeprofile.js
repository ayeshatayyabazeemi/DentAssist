document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.container');
    const empId = container.dataset.employeeId;

    const editBtn = document.getElementById('editBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    let editing = false;

    editBtn.addEventListener('click', () => {
        if(!editing) enableEdit();
        else saveChanges();
    });

    function enableEdit(){
        const fields = ['name','email','mobile_no','gender','dob','cnic','address'];
        fields.forEach(f=>{
            const span=document.getElementById(f);
            const val=span.innerText==='none'?'':span.innerText;
            span.innerHTML=`<input type="text" id="input_${f}" value="${val}">`;
        });

        // doctor schedule
        const schedTable=document.getElementById('scheduleTable');
        if(schedTable){
            Array.from(schedTable.querySelectorAll('tbody tr')).forEach(tr=>{
                const start=tr.cells[1].innerText;
                const end=tr.cells[2].innerText;
                tr.cells[1].innerHTML=`<input type="time" value="${start}">`;
                tr.cells[2].innerHTML=`<input type="time" value="${end}">`;
            });
        }

        editBtn.innerText='Save';
        editing=true;
    }

    function saveChanges(){
        const fields=['name','email','mobile_no','gender','dob','cnic','address'];
        let data={};
        fields.forEach(f=>{
            data[f]=document.getElementById(`input_${f}`).value.trim();
        });

        const schedTable=document.getElementById('scheduleTable');
        if(schedTable){
            data['schedule']=[];
            Array.from(schedTable.querySelectorAll('tbody tr')).forEach(tr=>{
                data['schedule'].push({
                    day: tr.dataset.day,
                    start_time: tr.cells[1].querySelector('input').value,
                    end_time: tr.cells[2].querySelector('input').value
                });
            });
        }

        fetch(`/api/employee/update/${empId}`,{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify(data)
        })
        .then(res=>res.json())
        .then(res=>{
            if(res.status==='success'){
                fields.forEach(f=>{
                    document.getElementById(f).innerText=data[f]||'none';
                });
                if(schedTable && data.schedule){
                    Array.from(schedTable.querySelectorAll('tbody tr')).forEach((tr,i)=>{
                        tr.cells[1].innerText=data.schedule[i].start_time;
                        tr.cells[2].innerText=data.schedule[i].end_time;
                    });
                }
                editBtn.innerText='Edit';
                editing=false;
                alert('Updated successfully!');
            } else alert('Failed: '+res.message);
        })
        .catch(err=>alert('Error: '+err));
    }

    deleteBtn.addEventListener('click', ()=>{
        if(!confirm('Are you sure you want to delete this employee?')) return;
        fetch(`/api/employee/delete/${empId}`,{method:'DELETE'})
        .then(res=>res.json())
        .then(res=>{
            if(res.status==='success'){
                alert('Deleted successfully');
                window.location.href='/adminDashboard';
            } else alert('Failed: '+res.message);
        })
        .catch(err=>alert('Error: '+err));
    });
});
