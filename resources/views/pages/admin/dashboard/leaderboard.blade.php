@php
    $site_currency = getParsedTemplate('site_currency');
@endphp
@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold">Dashboard</p>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="relative card bg-base-100 p-4 border border-base-300" x-data="chartSaleCountBar()" x-init="init()">
                <div class="flex flex-row justify-between items-start">
                    <div class="flex flex-col gap-1 mb-2">
                        <h2 class="font-semibold">Total Sale</h2>
                        <p class="text-sm">Total Value: <span x-text="total_value"></span></p>
                    </div>

                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                        <option value="last_2_year">Last 2 Years</option>
                        <option value="last_3_year">Last 3 Years</option>
                        <option value="last_5_year">Last 5 Years</option>
                        <option value="last_10_year">Last 10 Years</option>
                    </select>
                </div>

                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="relative card bg-base-100 p-4 border border-base-300" x-data="chartSaleAmountBar()"
                x-init="init()">
                <div class="flex flex-row justify-between items-start">
                    <div class="flex flex-col gap-1 mb-2">
                        <h2 class="font-semibold">Total Revenue</h2>
                        <p class="text-sm">Total Value: <span x-text="total_value"></span> {{ $site_currency }}</p>
                    </div>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                        <option value="last_2_year">Last 2 Years</option>
                        <option value="last_3_year">Last 3 Years</option>
                        <option value="last_5_year">Last 5 Years</option>
                        <option value="last_10_year">Last 10 Years</option>
                    </select>
                </div>

                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="relative card bg-base-100 p-4 border border-base-300" x-data="chartProfitAmountBar()"
                x-init="init()">
                <div class="flex flex-row justify-between items-start">
                    <div class="flex flex-col gap-1 mb-2">
                        <h2 class="font-semibold">Total Profit</h2>
                        <p class="text-sm">Total Value: <span x-text="total_value"></span> {{ $site_currency }}</p>
                    </div>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                        <option value="last_2_year">Last 2 Years</option>
                        <option value="last_3_year">Last 3 Years</option>
                        <option value="last_5_year">Last 5 Years</option>
                        <option value="last_10_year">Last 10 Years</option>
                    </select>
                </div>

                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="card bg-base-100 p-4 border border-base-300" x-data="chartSaleProductPie()" x-init="init()">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex flex-col gap-1 mb-2">
                        <h2 class="font-semibold">Top Selling Products</h2>
                        <p class="text-sm">Total Type: <span x-text="total_type"></span>, Total Count: <span x-text="total_count"></span></p>
                    </div>
                    <h2 class="font-semibold"></h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                        <option value="last_2_year">Last 2 Years</option>
                        <option value="last_3_year">Last 3 Years</option>
                        <option value="last_5_year">Last 5 Years</option>
                        <option value="last_10_year">Last 10 Years</option>
                    </select>
                </div>

                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="w-full min-h-[320px] sm:min-h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="card bg-base-100 p-4 border border-base-300" x-data="chartTopCategoryPie()" x-init="init()">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex flex-col gap-1 mb-2">
                        <h2 class="font-semibold">Top Selling Categories</h2>
                        <p class="text-sm">Total Type: <span x-text="total_type"></span>, Total Count: <span x-text="total_count"></span></p>
                    </div>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                        <option value="last_2_year">Last 2 Years</option>
                        <option value="last_3_year">Last 3 Years</option>
                        <option value="last_5_year">Last 5 Years</option>
                        <option value="last_10_year">Last 10 Years</option>
                    </select>
                </div>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="w-full min-h-[320px] sm:min-h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="card bg-base-100 p-4 border border-base-300" x-data="chartTopBrandPie()" x-init="init()">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex flex-col gap-1 mb-2">
                        <h2 class="font-semibold">Top Selling Brands</h2>
                        <p class="text-sm">Total Type: <span x-text="total_type"></span>, Total Count: <span x-text="total_count"></span></p>
                    </div>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                        <option value="last_2_year">Last 2 Years</option>
                        <option value="last_3_year">Last 3 Years</option>
                        <option value="last_5_year">Last 5 Years</option>
                        <option value="last_10_year">Last 10 Years</option>
                    </select>
                </div>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="w-full min-h-[320px] sm:min-h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function chartSaleCountBar() {
            return {
                loading: true,
                error: false,
                chart: null,
                duration: 'today',
                total_value: 0,
                async init() {
                    await this.fetchData();
                    this.loading = false;
                },
                async updateChart() {
                    this.loading = true;

                    await this.fetchData();

                    await this.$nextTick();
                    this.loading = false;
                },
                async fetchData() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/sale-count', {
                            params: {
                                duration: this.duration
                            }
                        });
                        const data = res.data.data;
                        this.total_value = data.total;
                        if (!this.chart) {
                            this.chart = new ApexCharts(this.$refs.chartContainer, {
                                chart: {
                                    type: "line",
                                    height: 280,
                                    zoom: {
                                        enabled: false
                                    },
                                    selection: {
                                        enabled: false
                                    }
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                series: [{
                                    name: "Sales",
                                    data: data.series
                                }],
                                xaxis: {
                                    categories: data.categories
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(value) {
                                            return parseFloat(value).toFixed(2);
                                        }
                                    }
                                },
                                tooltip: {
                                    y: {
                                        formatter: function(value) {
                                            return parseFloat(value).toFixed(2);
                                        }
                                    }
                                },
                                dataLabels: {
                                    formatter: function(value, {
                                        seriesIndex,
                                        w
                                    }) {
                                        let val = w.config.series[seriesIndex];
                                        return val.toFixed(2);
                                    }
                                }
                            });
                            this.chart.render();
                        } else {
                            this.chart.updateOptions({
                                xaxis: {
                                    categories: data.categories
                                }
                            });
                            this.chart.updateSeries([{
                                name: "Sales",
                                data: data.series
                            }]);
                        }
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                        this.total_value = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartSaleAmountBar() {
            return {
                loading: true,
                error: false,
                chart: null,
                duration: 'today',
                total_value: 0,
                async init() {
                    await this.fetchData();
                    this.loading = false;
                },
                async updateChart() {
                    this.loading = true;

                    await this.fetchData();

                    await this.$nextTick();
                    this.loading = false;
                },
                async fetchData() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/sale-amount', {
                            params: {
                                duration: this.duration
                            }
                        });
                        const data = res.data.data;
                        this.total_value = data.total;
                        if (!this.chart) {
                            this.chart = new ApexCharts(this.$refs.chartContainer, {
                                chart: {
                                    type: "line",
                                    height: 280,
                                    zoom: {
                                        enabled: false
                                    },
                                    selection: {
                                        enabled: false
                                    }
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                series: [{
                                    name: "Sales",
                                    data: data.series
                                }],
                                xaxis: {
                                    categories: data.categories
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(value) {
                                            return parseFloat(value).toFixed(2);
                                        }
                                    }
                                },
                                tooltip: {
                                    y: {
                                        formatter: function(value) {
                                            return parseFloat(value).toFixed(2);
                                        }
                                    }
                                },
                                dataLabels: {
                                    formatter: function(value, {
                                        seriesIndex,
                                        w
                                    }) {
                                        let val = w.config.series[seriesIndex];
                                        return val.toFixed(2);
                                    }
                                }
                            });
                            this.chart.render();
                        } else {
                            this.chart.updateOptions({
                                xaxis: {
                                    categories: data.categories
                                }
                            });
                            this.chart.updateSeries([{
                                name: "Sales",
                                data: data.series
                            }]);
                        }
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                        this.total_value = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartProfitAmountBar() {
            return {
                loading: true,
                error: false,
                chart: null,
                duration: 'today',
                total_value: 0,
                async init() {
                    await this.fetchData();
                    this.loading = false;
                },
                async updateChart() {
                    this.loading = true;

                    await this.fetchData();

                    await this.$nextTick();
                    this.loading = false;
                },
                async fetchData() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/profit-amount', {
                            params: {
                                duration: this.duration
                            }
                        });
                        const data = res.data.data;
                        this.total_value = data.total;
                        if (!this.chart) {
                            this.chart = new ApexCharts(this.$refs.chartContainer, {
                                chart: {
                                    type: "line",
                                    height: 280,
                                    zoom: {
                                        enabled: false
                                    },
                                    selection: {
                                        enabled: false
                                    }
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                series: [{
                                    name: "Sales",
                                    data: data.series
                                }],
                                xaxis: {
                                    categories: data.categories
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(value) {
                                            return parseFloat(value).toFixed(2);
                                        }
                                    }
                                },
                                tooltip: {
                                    y: {
                                        formatter: function(value) {
                                            return parseFloat(value).toFixed(2);
                                        }
                                    }
                                },
                                dataLabels: {
                                    formatter: function(value, {
                                        seriesIndex,
                                        w
                                    }) {
                                        let val = w.config.series[seriesIndex];
                                        return val.toFixed(2);
                                    }
                                }
                            });
                            this.chart.render();
                        } else {
                            this.chart.updateOptions({
                                xaxis: {
                                    categories: data.categories
                                }
                            });
                            this.chart.updateSeries([{
                                name: "Profits",
                                data: data.series
                            }]);
                        }
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                        this.total_value = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartSaleProductPie() {
            return {
                loading: true,
                error: false,
                chart: null,
                duration: 'today',
                total_type: 0,
                total_count:0,
                async init() {
                    await this.fetchData();
                    this.loading = false;
                },
                async updateChart() {
                    this.loading = true;
                    await this.fetchData();
                    await this.$nextTick();
                    this.loading = false;
                },
                async fetchData() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/sale-product-pie', {
                            params: {
                                duration: this.duration
                            }
                        });

                        const data = res.data.data;
                        this.total_type = data.total_type;
                        this.total_count = data.total_count;
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');

                        if (this.chart) this.chart.destroy();

                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300,
                                zoom: {
                                    enabled: false
                                },
                                selection: {
                                    enabled: false
                                }
                            },
                            labels: data.labels,
                            series: data.series,
                            tooltip: {
                                y: {
                                    formatter: function(value) {
                                        return parseFloat(value).toFixed(2);
                                    }
                                }
                            },
                            dataLabels: {
                                formatter: function(value, {
                                    seriesIndex,
                                    w
                                }) {
                                    const val = w.config.series[seriesIndex];
                                    return val.toFixed(2);
                                }
                            },
                            legend: {
                                position: 'right'
                            },
                            responsive: [{
                                breakpoint: 640,
                                options: {
                                    chart: {
                                        height: 280
                                    },
                                    legend: {
                                        position: 'bottom',
                                        fontSize: '12px'
                                    },
                                    dataLabels: {
                                        style: {
                                            fontSize: '11px'
                                        }
                                    }
                                }
                            }],
                        });
                        this.chart.render();
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                        this.total_type = 0;
                        this.total_count = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartTopCategoryPie() {
            return {
                loading: true,
                error: false,
                chart: null,
                duration: 'today',
                total_type: 0,
                total_count:0,
                async init() {
                    await this.fetchData();
                    this.loading = false;
                },
                async updateChart() {
                    this.loading = true;
                    await this.fetchData();
                    this.loading = false;
                },
                async fetchData() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/sale-category-pie', {
                            params: {
                                duration: this.duration
                            }
                        });
                        const data = res.data.data;
                        this.total_type = data.total_type;
                        this.total_count = data.total_count;
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300,
                                zoom: {
                                    enabled: false
                                },
                                selection: {
                                    enabled: false
                                }
                            },
                            labels: data.labels,
                            series: data.series,
                            legend: {
                                position: 'right'
                            },
                            responsive: [{
                                breakpoint: 640,
                                options: {
                                    chart: {
                                        height: 280
                                    },
                                    legend: {
                                        position: 'bottom',
                                        fontSize: '12px'
                                    },
                                    dataLabels: {
                                        style: {
                                            fontSize: '11px'
                                        }
                                    }
                                }
                            }],
                        });
                        this.chart.render();
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                        this.total_type = 0;
                        this.total_count = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartTopBrandPie() {
            return {
                loading: true,
                error: false,
                chart: null,
                duration: 'today',
                total_type: 0,
                total_count:0,
                async init() {
                    await this.fetchData();
                    this.loading = false;
                },
                async updateChart() {
                    this.loading = true;
                    await this.fetchData();
                    this.loading = false;
                },
                async fetchData() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/sale-brand-pie', {
                            params: {
                                duration: this.duration
                            }
                        });
                        const data = res.data.data;
                        this.total_type = data.total_type;
                        this.total_count = data.total_count;
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300,
                                zoom: {
                                    enabled: false
                                },
                                selection: {
                                    enabled: false
                                }
                            },
                            labels: data.labels,
                            series: data.series,
                            legend: {
                                position: 'right'
                            },
                            responsive: [{
                                breakpoint: 640,
                                options: {
                                    chart: {
                                        height: 280
                                    },
                                    legend: {
                                        position: 'bottom',
                                        fontSize: '12px'
                                    },
                                    dataLabels: {
                                        style: {
                                            fontSize: '11px'
                                        }
                                    }
                                }
                            }],
                        });
                        this.chart.render();
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                        this.total_type = 0;
                        this.total_count = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endpush
