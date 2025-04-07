@extends('layouts.app')

@section('content')
@include('buttons')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Period Filter -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Filtrer par période</h2>
            <div class="flex gap-4 items-end">
                <div class="w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Choisir la période</label>
                    <select id="periodSelect" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="day">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month" selected>Ce mois</option>
                        <option value="custom">Période personnalisée</option>
                    </select>
                </div>

                <!-- Custom Period Fields -->
                <div id="customPeriodFields" class="flex gap-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" id="startDate" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" id="endDate" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <button id="filterButton" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition-colors duration-200 font-medium">
                    Définir
                </button>
            </div>
        </div>

        <!-- Employees Grid -->
        <div id="employeesGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($employees as $employee)
            <a href="/employee-performance/{{ $employee->id }}" class="group">
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-200 transform hover:-translate-y-1">
                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mb-4 group-hover:bg-blue-200 transition">
                            <span class="text-3xl text-blue-600">
                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 text-center">{{ $employee->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $employee->secteur }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Ajouter juste après le div de filtre -->
<div id="alertMessage" class="hidden mb-4 px-4 py-3 rounded relative" role="alert"></div>

<!-- Modifier le script JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodSelect = document.getElementById('periodSelect');
        const customPeriodFields = document.getElementById('customPeriodFields');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const filterButton = document.getElementById('filterButton');
        const employeesGrid = document.getElementById('employeesGrid');
        const alertMessage = document.getElementById('alertMessage');

        periodSelect.addEventListener('change', function() {
            customPeriodFields.style.display = this.value === 'custom' ? 'flex' : 'none';
        });

        filterButton.addEventListener('click', function() {
            const selectedPeriod = periodSelect.value;

            if (selectedPeriod === 'custom') {
                if (!startDateInput.value || !endDateInput.value) {
                    showAlert('Veuillez sélectionner une date de début et de fin', 'error');
                    return;
                }
                filterEmployees(selectedPeriod, startDateInput.value, endDateInput.value);
            } else {
                filterEmployees(selectedPeriod);
            }
        });

        function showAlert(message, type = 'success') {
            alertMessage.className = 'mb-4 px-4 py-3 rounded relative';
            if (type === 'success') {
                alertMessage.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
            } else {
                alertMessage.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
            }
            alertMessage.innerHTML = message;
            alertMessage.classList.remove('hidden');

            // Faire disparaître le message après 5 secondes
            setTimeout(() => {
                alertMessage.classList.add('hidden');
            }, 5000);
        }

        async function filterEmployees(period, startDate = null, endDate = null) {
            try {
                const response = await fetch('/employee-performance/filter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        period,
                        start_date: startDate,
                        end_date: endDate
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    updateEmployeeGrid(data.employees);
                    showAlert(data.message);
                }
            } catch (error) {
                console.error('Error filtering employees:', error);
                showAlert('Une erreur est survenue lors du filtrage', 'error');
            }
        }

        function updateEmployeeGrid(employees) {
            const period = document.getElementById('periodSelect').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    employeesGrid.innerHTML = employees.map(employee => `
        <a href="/employee-performance/${employee.id}?period=${period}${period === 'custom' ? `&start_date=${startDate}&end_date=${endDate}` : ''}" class="group">
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-200 transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mb-4 group-hover:bg-blue-200 transition">
                                <span class="text-3xl text-blue-600">
                                    ${employee.name.substring(0, 2).toUpperCase()}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 text-center">${employee.name}</h3>
                            <p class="text-sm text-gray-500 mt-1">${employee.secteur}</p>
                        </div>
                    </div>
                </a>
            `).join('');
        }
    });
</script>
@endsection
