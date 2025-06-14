<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Data Validator Pro</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --dark: #1d3557;
      --light: #f8f9fa;
    }
    
    body {
      background: linear-gradient(135deg, var(--dark), #457b9d);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      min-height: 100vh;
    }
    
    .card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }
    
    .result-card {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease;
    }
    
    .result-card.show {
      opacity: 1;
      transform: translateY(0);
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    
    .loading {
      animation: pulse 1.5s infinite;
    }
    
    .progress-bar {
      transition: width 0.6s ease;
    }
  </style>
</head>
<body class="text-white p-4 md:p-8 flex items-center justify-center">
  <div class="max-w-4xl w-full">
    <div class="card rounded-xl shadow-2xl overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-center">
        <h1 class="text-3xl font-bold mb-2">
          <i class="fas fa-shield-alt mr-2"></i> Smart Data Validator Pro
        </h1>
        <p class="opacity-90">Advanced validation for your application data</p>
      </div>
      
      <!-- Form -->
      <div class="p-6 md:p-8">
        <form id="validatorForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name Field -->
            <div class="space-y-2">
              <label class="block font-medium text-gray-700">Full Name</label>
              <div class="relative">
                <input type="text" id="name" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                       placeholder="John Doe" required />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 hidden" id="nameLoading">
                  <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
              </div>
              <div class="text-sm text-gray-500">Must be 3-50 characters, letters only</div>
            </div>
            
            <!-- National ID Field -->
            <div class="space-y-2">
              <label class="block font-medium text-gray-700">National ID</label>
              <div class="relative">
                <input type="text" id="nationalId" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                       placeholder="14-digit ID" required />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 hidden" id="idLoading">
                  <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
              </div>
              <div class="text-sm text-gray-500">Enter your 14-digit national ID</div>
            </div>
            
            <!-- Password Field -->
            <div class="space-y-2">
              <label class="block font-medium text-gray-700">Password</label>
              <div class="relative">
                <input type="password" id="password" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                       placeholder="Strong Password" required />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 hidden" id="passLoading">
                  <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
              </div>
              <div class="text-sm text-gray-500">Min 12 chars with numbers & symbols</div>
            </div>
            
            <!-- Email Field -->
            <div class="space-y-2">
              <label class="block font-medium text-gray-700">Email</label>
              <div class="relative">
                <input type="email" id="email" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                       placeholder="example@mail.com" required />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 hidden" id="emailLoading">
                  <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
              </div>
              <div class="text-sm text-gray-500">We'll send a verification email</div>
            </div>
          </div>
          
          <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
            <span id="submitText">Validate All Fields</span>
            <span id="submitSpinner" class="ml-2 hidden">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
          </button>
        </form>
        
        <!-- Results Section -->
        <div id="results" class="mt-8 space-y-4 hidden">
          <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">
            <i class="fas fa-clipboard-check mr-2 text-indigo-600"></i> Validation Results
          </h3>
          
          <!-- Progress Bar -->
          <div class="bg-gray-100 rounded-full h-2.5">
            <div id="validationProgress" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
          </div>
          
          <div id="resultsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Results will be inserted here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('validatorForm');
    const resultsDiv = document.getElementById('results');
    const resultsContainer = document.getElementById('resultsContainer');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const progressBar = document.getElementById('validationProgress');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Reset UI
      resultsContainer.innerHTML = '';
      resultsDiv.classList.add('hidden');
      submitText.textContent = 'Validating...';
      submitSpinner.classList.remove('hidden');
      progressBar.style.width = '0%';
      
      // Get values
      const name = document.getElementById('name').value;
      const nationalId = document.getElementById('nationalId').value;
      const password = document.getElementById('password').value;
      const email = document.getElementById('email').value;
      
      try {
        // Show loading indicators
        document.getElementById('nameLoading').classList.remove('hidden');
        document.getElementById('idLoading').classList.remove('hidden');
        document.getElementById('passLoading').classList.remove('hidden');
        document.getElementById('emailLoading').classList.remove('hidden');
        
        // Validate all fields
        const responses = await Promise.all([
          validateField('name', name),
          validateField('national-id', nationalId),
          validateField('password', password),
          validateField('email', email)
        ]);
        
        // Process results
        showResults(responses);
        progressBar.style.width = '100%';
        
      } catch (error) {
        showError(error);
      } finally {
        // Hide loading indicators
        submitText.textContent = 'Validate All Fields';
        submitSpinner.classList.add('hidden');
        document.getElementById('nameLoading').classList.add('hidden');
        document.getElementById('idLoading').classList.add('hidden');
        document.getElementById('passLoading').classList.add('hidden');
        document.getElementById('emailLoading').classList.add('hidden');
      }
    });
    
    async function validateField(type, value) {
      try {
        const response = await axios.post(`/api/validate/${type}`, 
          type === 'national-id' ? { national_id: value } : { [type]: value }
        );
        
        return {
          type,
          success: true,
          data: response.data,
          message: response.data.message || 'Validation successful'
        };
      } catch (error) {
        return {
          type,
          success: false,
          data: error.response?.data,
          message: error.response?.data?.message || 'Validation failed'
        };
      }
    }
    
    function showResults(responses) {
      resultsDiv.classList.remove('hidden');
      
      responses.forEach((res, index) => {
        setTimeout(() => {
          const card = document.createElement('div');
          card.className = `result-card bg-white p-4 rounded-lg shadow border-l-4 ${
            res.success ? 'border-green-500' : 'border-red-500'
          }`;
          
          card.innerHTML = `
            <div class="flex items-start">
              <div class="flex-shrink-0 mt-1">
                <i class="fas fa-${getIcon(res.type)} text-${res.success ? 'green' : 'red'}-500 mr-2"></i>
              </div>
              <div>
                <h4 class="font-bold text-gray-800 capitalize">${res.type.replace('-', ' ')}</h4>
                <p class="${res.success ? 'text-green-600' : 'text-red-600'} font-medium">
                  ${res.message}
                </p>
                ${res.success ? '' : `<p class="text-sm text-gray-600 mt-1">${res.data?.errors?.join(', ') || ''}</p>`}
                
                <div class="mt-2 text-xs text-gray-500 overflow-x-auto">
                  <pre>${JSON.stringify(res.data, null, 2)}</pre>
                </div>
              </div>
            </div>
          `;
          
          resultsContainer.appendChild(card);
          
          // Trigger animation
          setTimeout(() => card.classList.add('show'), 50);
          
          // Update progress bar
          progressBar.style.width = `${((index + 1) / responses.length) * 100}%`;
        }, index * 200);
      });
    }
    
    function showError(error) {
      resultsDiv.classList.remove('hidden');
      
      const errorCard = document.createElement('div');
      errorCard.className = 'result-card bg-red-50 p-4 rounded-lg shadow border-l-4 border-red-500';
      errorCard.innerHTML = `
        <div class="flex items-start">
          <div class="flex-shrink-0 mt-1">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
          </div>
          <div>
            <h4 class="font-bold text-gray-800">Validation Error</h4>
            <p class="text-red-600">${error.message || 'Something went wrong during validation'}</p>
            ${error.response?.data ? `
              <div class="mt-2 text-xs text-gray-500 overflow-x-auto">
                <pre>${JSON.stringify(error.response.data, null, 2)}</pre>
              </div>
            ` : ''}
          </div>
        </div>
      `;
      
      resultsContainer.appendChild(errorCard);
      setTimeout(() => errorCard.classList.add('show'), 50);
    }
    
    function getIcon(type) {
      const icons = {
        'name': 'user',
        'national-id': 'id-card',
        'password': 'lock',
        'email': 'envelope'
      };
      return icons[type] || 'check-circle';
    }
  </script>
</body>
</html>