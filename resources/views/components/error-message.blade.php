@props(['error'])

<div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-6 mb-6 shadow-sm">

    <div class="flex items-center space-x-3 mb-4">

        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">

            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

        </svg>

        <h4 class="text-xl font-semibold text-red-700">Erreur !</h4>

    </div>

    <p class="text-red-600 mb-4 text-lg">{{ $error }}</p>

    <div class="h-px bg-red-200 my-4"></div>

    <div class="text-red-600">

        <p class="font-semibold text-lg mb-3">Suggestions :</p>

        <ul class="list-none space-y-2 pl-2">

            <li class="flex items-center space-x-2">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />

                </svg>

                <span>Essayez d'être plus précis</span>

            </li>

            <li class="flex items-center space-x-2">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />

                </svg>

                <span>Utilisez des termes spécifiques</span>

            </li>

        </ul>

    </div>

</div>
