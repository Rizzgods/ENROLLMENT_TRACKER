<div>
  <!-- Flex layout for the stats container -->
  <div class="mt-6 flex justify-center gap-8 sm:mt-8">
    <!-- Each item within the flex container -->
    <div class="flex flex-col justify-end rounded-lg bg-blue-50 px-4 py-8 text-center w-64 h-40">
      <?php
      if ($row = $total->fetch_row()) {
        echo '<div class="flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl mr-2">' . number_format($row[0]) . '</dd>
                
              </div>';
      }
      ?>
      <dt class="text-lg font-medium text-gray-500">Current Enrollees</dt>
    </div>

    <!-- Second stats container -->
    <div class="flex flex-col justify-end rounded-lg bg-blue-50 px-4 py-8 text-center w-64 h-40">
    <?php
      if ($row = $total_course->fetch_row()) {
        echo '<div class="flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-4-9-4-9 4 9 4zm0-10l9-4-9-4-9 4 9 4z"></path>
                </svg>
                <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl mr-2">' . number_format($row[0]) . '</dd>
                
              </div>';
      }
      ?>
      <dt class="text-lg font-medium text-gray-500">Total Courses Offered</dt>
    </div>
  </div>
</div>
