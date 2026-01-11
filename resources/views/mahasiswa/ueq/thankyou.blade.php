    <div class="py-24 flex items-center justify-center min-h-[70vh]">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden group">
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-16 text-white relative">
                    <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-1000"></div>
                    
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-xl border border-white/30 animate-in zoom-in-50 duration-700">
                            <i class="fas fa-heart text-4xl animate-pulse"></i>
                        </div>
                        <h2 class="text-4xl font-bold  tracking-widest mb-4 uppercase">Terima Kasih!</h2>
                        <p class="text-blue-100 text-lg font-medium ">Feedback Anda sangat berharga bagi kami.</p>
                    </div>
                </div>
                
                <div class="p-12">
                    <p class="text-gray-500 text-lg mb-10 leading-relaxed font-medium">
                        Kami sangat menghargai waktu yang Anda luangkan untuk mengisi survey UEQ ini. 
                        Setiap masukan akan kami gunakan untuk menyempurnakan pengalaman belajar di <span class="text-blue-600 font-bold uppercase">OOPEDIA</span>.
                    </p>

                    <div class="space-y-4">
                        <a href="{{ route('mahasiswa.dashboard') }}" 
                           class="inline-flex items-center justify-center gap-3 px-10 py-5 bg-gray-900 text-white rounded-2xl font-bold  uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-gray-200 hover:shadow-blue-200 group/btn">
                            <i class="fas fa-home group-hover:-translate-y-1 transition-transform"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
