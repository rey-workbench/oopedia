<div class="exercise-feedback" style="display: none;">
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
      <div id="feedbackBanner" class="p-12 text-center text-white relative transition-colors duration-500">
        <div class="absolute inset-0 bg-gradient-to-br opacity-90" id="feedbackGradient"></div>
        
        <div class="relative z-10">
          <div id="feedbackIconWrapper" class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-xl border border-white/30 transition-transform duration-700">
            <div id="feedbackIcon"></div>
          </div>
          <h2 id="feedbackStatus" class="text-4xl font-bold tracking-widest uppercase mb-2"></h2>
          <p id="feedbackSubStatus" class="text-white/80 font-medium"></p>
        </div>
      </div>
      
      <div class="p-10">
        <div id="feedbackDetails">
          <div id="explanationBox" class="bg-gray-50 rounded-3xl p-8 border border-gray-100 mb-8" style="display: none;">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shadow-inner">
                <i class="fas fa-lightbulb text-sm"></i>
              </div>
              <h5 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Analisis Jawaban</h5>
            </div>
            <div id="explanationText" class="text-gray-600 leading-relaxed font-medium"></div>
          </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button id="tryAgainBtn" style="display: none;" 
              class="flex items-center justify-center gap-3 px-10 py-4 bg-amber-500 text-white rounded-2xl font-bold uppercase tracking-widest transition-all shadow-lg shadow-amber-100 hover:shadow-amber-200 hover:-translate-y-1">
            <i class="fas fa-redo"></i>
            Coba Lagi
          </button>
          <button id="nextQuestionBtn" style="display: none;" 
              class="flex items-center justify-center gap-3 px-10 py-4 bg-gray-900 text-white rounded-2xl font-bold uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-gray-200 hover:shadow-blue-200 hover:-translate-y-1">
            Lanjut ke Soal Berikutnya
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div> 