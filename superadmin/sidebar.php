<!-- Sidebar with inline onclick handlers as backup -->
<aside class="bg-gradient-to-r from-blue-600 to-blue-800 text-white h-screen p-4 md:p-6 fixed top-0 left-0 w-32">
    <div class="container mx-auto max-w-[1920px] px-4 h-full flex flex-col justify-between">
        <div class="flex flex-col items-center space-y-4">
            <a href="#" class="text-white hover:text-blue-200 transition duration-150">
                <img src="assets/logo" alt="Logo" class="h-10 w-10">
            </a>
        </div>
        <div class="flex flex-col space-y-4 mt-8 flex-grow">
            <!-- Home Button with inline onclick as backup -->
            <a href="#" id="home-btn" class="bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg"
               onclick="document.getElementById('list-content').style.display='block'; 
                       document.getElementById('account-content').style.display='none'; 
                       document.getElementById('logs-content').style.display='none';
                       this.className='bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
                       document.getElementById('account-btn').className='bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
                       document.getElementById('logs-btn').className='bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
                       return false;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
              
            </a>
            
            <!-- Account Button with inline onclick as backup -->
            <a href="#" id="account-btn" class="bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg"
               onclick="document.getElementById('list-content').style.display='none'; 
                       document.getElementById('account-content').style.display='block'; 
                       document.getElementById('logs-content').style.display='none';
                       this.className='bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
                       document.getElementById('home-btn').className='bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
                       document.getElementById('logs-btn').className='bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
                       return false;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 mx-auto">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
             
            </a>
            
            <!-- Logs Button with inline onclick as backup - FIXED -->
            <a href="#" id="logs-btn" class="bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg"
               onclick="document.getElementById('list-content').style.display='none'; 
                       document.getElementById('account-content').style.display='none'; 
                       document.getElementById('logs-content').style.display='block';
                       this.className='bg-white text-blue-700 hover:text-blue-800 transition duration-150 text-center p-2 rounded-lg';
                       document.getElementById('home-btn').className='bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
                       document.getElementById('account-btn').className='bg-blue-700 text-white hover:text-blue-200 transition duration-150 text-center p-2 rounded-lg';
                       return false;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 mx-auto">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
          
            </a>
        </div>
        
        <div class="flex flex-col space-y-4">
            <?php if (isset($_SESSION['USERNAME'])): ?>
                <a href="logout.php" class="text-white hover:text-blue-200 transition duration-150 text-center p-2 bg-red-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                
                </a>
            <?php endif; ?>
        </div>
    </div>
</aside>