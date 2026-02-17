document.addEventListener('DOMContentLoaded', function () {
    const patientInput = document.getElementById('patient_name');
    const patientIdInput = document.getElementById('patient_id');
    const suggestionsBox = document.getElementById('patient_suggestions');
    let timeout = null;

    patientInput.addEventListener('input', function () {
        const query = this.value.trim();

        if (timeout) clearTimeout(timeout);

        if (query.length < 2) {
            suggestionsBox.innerHTML = '';
            suggestionsBox.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/api/patient/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.status === 'success' && data.data.length) {
                        data.data.forEach(patient => {
                            const div = document.createElement('div');
                            div.classList.add('suggestion-item');
                            div.textContent = `${patient.name} (${patient.mobile_no})`;
                            div.dataset.id = patient.patient_id;
                            div.dataset.name = patient.name;

                            div.addEventListener('click', function () {
                                patientInput.value = this.dataset.name;
                                patientIdInput.value = this.dataset.id;
                                suggestionsBox.innerHTML = '';
                                suggestionsBox.style.display = 'none';
                            });

                            suggestionsBox.appendChild(div);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Patient search error:', err);
                    suggestionsBox.style.display = 'none';
                });
        }, 300);
    });

    // Hide suggestions on outside click
    document.addEventListener('click', function (e) {
        if (!suggestionsBox.contains(e.target) && e.target !== patientInput) {
            suggestionsBox.innerHTML = '';
            suggestionsBox.style.display = 'none';
        }
    });
});
