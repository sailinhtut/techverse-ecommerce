@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5">
        <p class="lg:text-lg font-semibold">Dashboard</p>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="relative card bg-base-100 p-4 border border-base-300" x-data="chartSaleCountBar()" x-init="init()">
                <div class="flex flex-row justify-between items-center">
                    <h2 class="font-semibold mb-2">Total Sale</h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
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
                <div class="flex flex-row justify-between items-center">
                    <h2 class="font-semibold mb-2">Total Revenue</h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
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
                <div class="flex flex-row justify-between items-center">
                    <h2 class="font-semibold mb-2">Total Profit</h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>

                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="card bg-base-100 p-4 border border-base-300" x-data="chartSaleProductPie()" x-init="init()">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="font-semibold">Top Selling Products</h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>

                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="card bg-base-100 p-4 border border-base-300" x-data="chartTopCategoryPie()" x-init="init()">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="font-semibold">Top Selling Categories</h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

                <p x-show="error" class="text-red-500 mt-3">Failed to load chart.</p>
            </div>

            <div class="card bg-base-100 p-4 border border-base-300" x-data="chartTopBrandPie()" x-init="init()">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="font-semibold">Top Selling Brands</h2>
                    <select class="select w-max" x-model="duration" @change="updateChart()">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-base-100/70 z-10">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div class="h-[300px]" x-ref="chartContainer"></div>

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
                        if (!this.chart) {
                            this.chart = new ApexCharts(this.$refs.chartContainer, {
                                chart: {
                                    type: "line",
                                    height: 280
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
                        if (!this.chart) {
                            this.chart = new ApexCharts(this.$refs.chartContainer, {
                                chart: {
                                    type: "line",
                                    height: 280
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
                        if (!this.chart) {
                            this.chart = new ApexCharts(this.$refs.chartContainer, {
                                chart: {
                                    type: "line",
                                    height: 280
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
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');

                        if (this.chart) this.chart.destroy();

                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300
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
                            }
                        });
                        this.chart.render();
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartPaymentPie() {
            return {
                loading: true,
                error: false,
                chart: null,
                async init() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/payment-pie');
                        const data = res.data.data;
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');

                        if (this.chart) {
                            this.chart.destroy();
                        }

                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300
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
                            }
                        });
                        this.chart.render();
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function chartDevicePie() {
            return {
                loading: true,
                error: false,
                chart: null,
                async init() {
                    try {
                        const res = await axios.get('/admin/dashboard/api/device-pie');
                        const data = res.data.data;
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');

                        if (this.chart) {
                            this.chart.destroy();
                        }

                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300
                            },
                            labels: data.labels,
                            series: data.series
                        });
                        this.chart.render();
                    } catch (e) {
                        console.error(e);
                        this.error = true;
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
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300
                            },
                            labels: data.labels,
                            series: data.series
                        });
                        this.chart.render();
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
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
                        if (!data?.labels || !data?.series) throw new Error('Invalid data');
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chartContainer, {
                            chart: {
                                type: 'pie',
                                height: 300
                            },
                            labels: data.labels,
                            series: data.series
                        });
                        this.chart.render();
                        this.error = false;
                    } catch (e) {
                        console.error(e);
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endpush
