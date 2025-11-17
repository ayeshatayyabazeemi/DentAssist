document.addEventListener('DOMContentLoaded', () => {
  const editBtn = document.getElementById('editPatient');
  const saveBtn = document.getElementById('savePatient');
  const deleteBtn = document.getElementById('deletePatient');
  const fields = ['name','mobile_no','email','gender','dob','address','occupation','guardianname','guardianphonenumber','guardianrelation','insurance','doctorName','cnic'];

  editBtn.addEventListener('click',()=>{
    fields.forEach(f=>{
      document.getElementById(f+'Span').style.display='none';
      document.getElementById(f+'Input').style.display='block';
    });
    saveBtn.style.display='block';
  });

  saveBtn.addEventListener('click',async ()=>{
    const patientId = deleteBtn.dataset.id;
    const data={};
    fields.forEach(f=>data[f]=document.getElementById(f+'Input').value);

    try{
      const res=await fetch(`/api/patient/update/${patientId}`,{
        method:'PUT',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(data)
      });
      const json=await res.json();
      if(json.status==='success'){
        fields.forEach(f=>{
          document.getElementById(f+'Span').textContent=data[f];
          document.getElementById(f+'Span').style.display='block';
          document.getElementById(f+'Input').style.display='none';
        });
        saveBtn.style.display='none';
        alert('Patient updated successfully!');
      } else alert(json.message);
    }catch(e){alert('Error updating patient');}
  });

  deleteBtn.addEventListener('click',async ()=>{
    if(!confirm('Are you sure you want to delete this patient?')) return;
    const patientId=deleteBtn.dataset.id;
    try{
      const res=await fetch(`/api/patient/delete/${patientId}`,{method:'DELETE'});
      const json=await res.json();
      if(json.status==='success'){
        alert('Patient deleted successfully!');
        window.location.href='/adminDashboard';
      } else alert(json.message);
    }catch(e){alert('Error deleting patient');}
  });
});
