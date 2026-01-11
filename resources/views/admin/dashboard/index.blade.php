<x-layouts.app title="Admin Dashboard" theme="admin">
  <div class="space-y-12">
    <x-ui.page-header
      title="Dashboard"
      subtitle="Pusat kendali operasional dan visualisasi data sistem OOPedia."
    />

    {{-- Main Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <x-ui.stat-card 
        title="Total Mahasiswa" 
        :value="$totalStudents" 
        icon="fas fa-users-viewfinder" 
        variant="primary"
        footer="Entitas terdaftar"
      />
      <x-ui.stat-card 
        title="Node Aktif" 
        :value="$activeStudents" 
        icon="fas fa-signal" 
        variant="success"
        footer="Sesi aktif hari ini"
      />
      <x-ui.stat-card 
        title="Modul Instruksional" 
        :value="$totalMaterials" 
        icon="fas fa-folder-tree" 
        variant="primary"
        footer="Konten aktif"
      />
      <x-ui.stat-card 
        title="Korpus Evaluasi" 
        :value="$totalQuestions" 
        icon="fas fa-microchip" 
        variant="success"
        footer="Total butir evaluasi"
      />
    </div>

    {{-- Analytics Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <x-ui.card class="border-slate-100 shadow-3xl relative overflow-hidden bg-white/50 backdrop-blur-xl">
        <x-slot:header>
          <div class="flex items-center justify-between w-full px-8 py-6 bg-white/80">
            <div class="flex items-center gap-5">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                <i class="fas fa-users-rays text-xl"></i>
              </div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Distribusi Progres Cohort</p>
            </div>
          </div>
        </x-slot:header>
        <div class="p-8">
          <div id="progressDistributionChart" class="min-h-[450px]"></div>
        </div>
        {{-- Decorative background element --}}
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-indigo-50/40 rounded-full blur-[100px] -z-10"></div>
      </x-ui.card>

      <x-ui.card class="border-slate-100 shadow-3xl relative overflow-hidden bg-white/50 backdrop-blur-xl">
        <x-slot:header>
          <div class="flex items-center justify-between w-full px-8 py-6 bg-white/80">
            <div class="flex items-center gap-5">
              <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner">
                <i class="fas fa-spider text-xl"></i>
              </div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Balance Mastering Modul</p>
            </div>
          </div>
        </x-slot:header>
        <div class="p-8">
          <div id="modulePerformanceChart" class="min-h-[450px]"></div>
        </div>
        {{-- Decorative background element --}}
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-emerald-50/40 rounded-full blur-[100px] -z-10"></div>
      </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
      {{-- Top Students Table --}}
      <div class="lg:col-span-2">
        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
          <x-slot:header>
            <div class="flex items-center justify-between w-full">
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Matriks Performa Utama</p>
              <x-ui.button variant="ghost" size="sm" :href="route('admin.students.index')" icon="fas fa-arrow-right">DATA GLOBAL</x-ui.button>
            </div>
          </x-slot:header>
          <x-ui.table>
            <x-slot:thead>
              <tr>
                <x-ui.th>Identitas Subjek</x-ui.th>
                <x-ui.th class="text-center">Jumlah Evaluasi</x-ui.th>
                <x-ui.th class="text-center">Progres Sinkronisasi</x-ui.th>
                <x-ui.th class="text-right">Aksi</x-ui.th>
              </tr>
            </x-slot:thead>
            @foreach($studentProgress as $student)
              <tr class="group hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs">
                      {{ substr($student->name, 0, 1) }}
                    </div>
                    <div>
                      <div class="font-bold text-slate-900 tracking-widest leading-none mb-1">{{ $student->name }}</div>
                      <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $student->email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="text-sm font-bold text-slate-900">{{ $student->completed_questions }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-col gap-2">
                    <div class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-slate-400">
                      <span>Progres</span>
                      <span>{{ $student->materials_progress }}%</span>
                    </div>
                    <x-ui.progress-bar :value="$student->materials_progress" size="xs" :showPercentage="false" variant="primary" />
                  </div>
                </td>
                <td class="px-6 py-4 text-right">
                  <x-ui.button variant="ghost" size="sm" :href="route('admin.students.progress', $student->id)" icon="fas fa-microscope" />
                </td>
              </tr>
            @endforeach
          </x-ui.table>
        </x-ui.card>
      </div>

      {{-- Popular Materials --}}
      <div>
        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
          <x-slot:header>
             <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Heatmap Konten</p>
          </x-slot:header>
          <div class="space-y-4 p-6 bg-slate-50/50">
            @foreach($popularMaterials as $material)
              <div class="flex items-center gap-4 p-4 rounded-3xl bg-white border border-slate-100 group hover:border-blue-200 transition-all shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center shadow-lg text-white transition-transform group-hover:scale-110">
                  <i class="fas fa-layer-group text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <h5 class="text-xs font-bold tracking-widest text-slate-900 truncate mb-1">{{ $material->title }}</h5>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $material->students_count }} Subjek</p>
                </div>
                <span class="text-xs font-bold text-blue-600">{{ $material->completion_rate }}%</span>
              </div>
            @endforeach
          </div>
        </x-ui.card>
      </div>
    </div>

    {{-- Recent Activity Timeline --}}
    <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
      <x-slot:header>
         <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Log Operasi (Langsung)</p>
      </x-slot:header>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 p-8">
        @foreach($recentProgress as $progress)
          <div class="relative p-6 rounded-[2.5rem] bg-slate-50 border border-slate-100 group hover:bg-white transition-colors">
            <div class="absolute top-6 right-6">
              <x-ui.badge variant="{{ $progress->is_correct ? 'success' : 'warning' }}" size="xs">
                {{ strtoupper($progress->question->complexity ?? 'LVL') }}
              </x-ui.badge>
            </div>
            
            <div class="flex flex-col gap-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg {{ $progress->is_correct ? 'bg-emerald-500' : 'bg-amber-500' }} text-white flex items-center justify-center text-[10px] shadow-lg shadow-emerald-500/20">
                  <i class="fas {{ $progress->is_correct ? 'fa-check' : 'fa-hourglass-start' }}"></i>
                </div>
                <div class="font-bold text-slate-900 uppercase tracking-widest text-xs">
                  {{ optional($progress->user)->name ?? 'ENT-TIDAK DIKETAHUI' }}
                </div>
              </div>
              
              <p class="text-[11px] font-bold text-slate-500 leading-relaxed">
                {{ $progress->is_correct ? 'Berhasil mendekripsi' : 'Menganalisis' }} modul <span class="text-slate-900 underline decoration-blue-200 underline-offset-4">{{ optional($progress->question->material)->title ?? '-' }}</span>
              </p>
              
              <div class="pt-4 border-t border-slate-200 flex justify-between items-center text-[9px] font-bold text-slate-300 uppercase tracking-widest">
                <span>{{ $progress->created_at->diffForHumans() }}</span>
                <i class="fas fa-bolt text-blue-500 opacity-20"></i>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </x-ui.card>
  </div>

  <x-admin.tutorial />
  
  <x-slot:scripts>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
      // Theme configuration
      const colors = {
        primary: '#4f46e5',
        success: '#10b981',
        slate: '#64748b'
      };

      // 1. Progress Distribution Chart
      var distributionData = {!! json_encode($studentAnalytics['distribution']) !!};
      var distributionOptions = {
        series: [{
          name: 'Jumlah Mahasiswa',
          data: Object.values(distributionData)
        }],
        chart: {
          type: 'bar',
          height: 450,
          toolbar: { show: false },
          fontFamily: 'Poppins, sans-serif',
          animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 1000,
            animateGradually: {
              enabled: true,
              delay: 150
            },
            dynamicAnimation: {
              enabled: true,
              speed: 350
            }
          }
        },
        plotOptions: {
          bar: {
            borderRadius: 20,
            columnWidth: '60%',
            distributed: true,
            dataLabels: {
              position: 'top',
            },
          }
        },
        colors: ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'],
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'light',
            type: "vertical",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
          },
        },
        dataLabels: { 
          enabled: true,
          formatter: function (val) {
            return val;
          },
          offsetY: -30,
          style: {
            fontSize: '12px',
            fontWeight: '900',
            colors: ["#64748b"]
          }
        },
        legend: { show: false },
        grid: {
          borderColor: '#f1f5f9',
          strokeDashArray: 4,
          yaxis: { lines: { show: true } }
        },
        xaxis: {
          categories: Object.keys(distributionData),
          labels: {
            style: {
              colors: '#94a3b8',
              fontSize: '12px',
              fontWeight: 800
            }
          },
          axisBorder: { show: false },
          axisTicks: { show: false }
        },
        yaxis: {
          labels: {
            style: {
              colors: '#94a3b8',
              fontSize: '12px',
              fontWeight: 800
            }
          }
        },
        tooltip: {
          theme: 'dark',
          custom: function({series, seriesIndex, dataPointIndex, w}) {
            return '<div class="px-3 py-2 bg-slate-900 text-white text-[12px] font-bold rounded-xl border border-slate-800">' +
              '<span>' + series[seriesIndex][dataPointIndex] + ' MAHASISWA</span>' +
              '</div>';
          }
        }
      };

      var distChart = new ApexCharts(document.querySelector("#progressDistributionChart"), distributionOptions);
      distChart.render();

      // 2. Module Performance Radar Chart
      var modulePerformance = {!! json_encode($studentAnalytics['modulePerformance']) !!};
      var radarOptions = {
        series: [{
          name: 'Completion Rate',
          data: modulePerformance.data
        }],
        chart: {
          height: 450,
          type: 'radar',
          toolbar: { show: false },
          fontFamily: 'Poppins, sans-serif',
          offsetY: 10,
          dropShadow: {
            enabled: true,
            blur: 8,
            left: 1,
            top: 1,
            opacity: 0.1
          }
        },
        colors: ['#4f46e5'],
        fill: {
          opacity: 0.4,
          type: 'gradient',
          gradient: {
            shade: 'dark',
            gradientToColors: ['#10b981'],
            shadeIntensity: 1,
            type: 'horizontal',
            opacityFrom: 0.6,
            opacityTo: 0.8,
            stops: [0, 100]
          }
        },
        stroke: {
          show: true,
          width: 3,
          colors: ['#4f46e5'],
          dashArray: 0
        },
        markers: {
          size: 6,
          colors: ['#fff'],
          strokeColors: '#4f46e5',
          strokeWidth: 3,
          hover: {
            size: 9
          }
        },
        xaxis: {
          categories: modulePerformance.labels,
          labels: {
            show: true,
            style: {
              colors: ["#94a3b8", "#94a3b8", "#94a3b8", "#94a3b8", "#94a3b8"],
              fontSize: "11px",
              fontWeight: 900
            }
          }
        },
        yaxis: {
          show: false,
          max: 100
        },
        plotOptions: {
          radar: {
            size: 180,
            polygons: {
              strokeColors: '#f1f5f9',
              connectorColors: '#f1f5f9',
              fill: {
                colors: ['#f8fafc', '#fff']
              }
            }
          }
        },
        grid: { show: false },
        tooltip: {
          theme: 'dark',
          y: {
            formatter: function(val) {
              return val + "% SELESAI";
            }
          }
        }
      };

      var radarChart = new ApexCharts(document.querySelector("#modulePerformanceChart"), radarOptions);
      radarChart.render();
    </script>
  </x-slot:scripts>
</x-layouts.app>
