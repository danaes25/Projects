const dpCode =
 `int dpStockProfit(const vector<int>& prices, int k, int fee) {
    int n = prices.size();
    if (n == 0 || k == 0) return 0;

    vector<int> hold(k + 1, INT_MIN);
    vector<int> notHold(k + 1, 0);

    for (int t = 0; t <= k; ++t)
        hold[t] = -prices[0];

    for (int i = 1; i < n; ++i) {
        for (int t = 1; t <= k; ++t) {
            notHold[t] = max(notHold[t], hold[t] + prices[i] - fee);
            hold[t] = max(hold[t], notHold[t - 1] - prices[i]);
        }
    }

    return notHold[k];
}`;

const greedyCode =
 `int greedyStockProfit(const vector<int>& prices, int k, int fee) {
    int n = prices.size();
    if (n == 0 || k == 0) return 0;

    int profit = 0;
    int transactions = 0;
    int buyPrice = INT_MAX;

    for (int price : prices) {
        buyPrice = min(buyPrice, price);

        if (price - buyPrice > fee) {
            profit += price - buyPrice - fee;
            transactions++;
            if (transactions >= k) break;
            buyPrice = price - fee;
        }
    }

    return profit;
}`;

// Display the code snippets
document.getElementById('dp-code').textContent = dpCode;
document.getElementById('greedy-code').textContent = greedyCode;

// Initialize chart
let priceChart = null;
let dpStates = [];
let greedyStates = [];

function dpStockProfit(prices, k, fee) {
    const n = prices.length;
    if (n === 0 || k === 0) return 0;

    const hold = new Array(k + 1).fill(-Infinity);
    const notHold = new Array(k + 1).fill(0);
    dpStates = []; // Reset states

    
    // Initial state
    for (let t = 0; t <= k; t++) {
        if(prices[t] < 0) continue; // Skip negative prices
        hold[t] = -prices[0];
    }
    dpStates.push({
        day: 0,
        price: prices[0],
        hold: [...hold],
        notHold: [...notHold],
        action: 'Initial State'
    });
    
    for (let i = 1; i < n; i++) {
        if(prices[i] < 0) continue; // Skip negative prices
        for (let t = 1; t <= k; t++) {
            const oldNotHold = notHold[t];
            const oldHold = hold[t];
            
            notHold[t] = Math.max(notHold[t], hold[t] + prices[i] - fee);
            hold[t] = Math.max(hold[t], notHold[t - 1] - prices[i]);

            // Record state change if there was a change
            if (oldNotHold !== notHold[t] || oldHold !== hold[t]) {
                dpStates.push({
                    day: i,
                    price: prices[i],
                    hold: [...hold],
                    notHold: [...notHold],
                    action: oldNotHold !== notHold[t] ? 'Sell' : 'Buy'
                });
            }
        }
    }

    return notHold[k];
}

function greedyStockProfit(prices, k, fee) {
    const n = prices.length;
    if (n === 0 || k === 0) return 0;

    let profit = 0;
    let transactions = 0;
    let buyPrice = Infinity;
    greedyStates = []; // Reset states

    for (let i = 0; i < n; i++) {
        if(prices[i] < 0) continue; // Skip negative prices
        
        const price = prices[i];
        buyPrice = Math.min(buyPrice, price);

        // Record state before potential transaction
        greedyStates.push({
            day: i,
            price: price,
            buyPrice: buyPrice,
            profit: profit,
            transactions: transactions,
            action: 'Check'
        });

        if (price - buyPrice > fee) {
            profit += price - buyPrice - fee;
            transactions++;
            
            // Record transaction
            greedyStates.push({
                day: i,
                price: price,
                buyPrice: buyPrice,
                profit: profit,
                transactions: transactions,
                action: 'Sell'
            });

            if (transactions >= k) break;
            buyPrice = price - fee;
        }
    }

    return profit;
}

function updateVisualization() {
    const ctx = document.getElementById('priceChart').getContext('2d');
    
    if (priceChart) {
        priceChart.destroy();
    }

    const prices = document.getElementById('prices').value.split(',').map(price => parseInt(price.trim()));
    const k = parseInt(document.getElementById('k').value);
    const fee = parseInt(document.getElementById('fee').value);

    // Calculate results and populate states
    const dpResult = dpStockProfit(prices, k, fee);
    const greedyResult = greedyStockProfit(prices, k, fee);

    // Create datasets for both algorithms
    const datasets = [{
        label: 'Stock Prices',
        data: prices.map((price, index) => ({
            x: index,
            y: price
        })),
        borderColor: '#3498db',
        backgroundColor: 'rgba(52, 152, 219, 0.1)',
        tension: 0.1,
        fill: true,
        borderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6
    }];

    // Add DP algorithm points
    if (dpStates.length > 0) {
        datasets.push({
            label: 'DP Algorithm Actions',
            data: dpStates.map(state => ({
                x: state.day,
                y: state.price,
                action: state.action
            })),
            pointBackgroundColor: '#e74c3c',
            pointBorderColor: '#c0392b',
            pointRadius: 6,
            pointHoverRadius: 8,
            showLine: false,
            pointStyle: 'circle',
            borderWidth: 2
        });
    }

    // Add Greedy algorithm points
    if (greedyStates.length > 0) {
        datasets.push({
            label: 'Greedy Algorithm Actions',
            data: greedyStates.map(state => ({
                x: state.day,
                y: state.price,
                action: state.action
            })),
            pointBackgroundColor: '#2ecc71',
            pointBorderColor: '#27ae60',
            pointRadius: 6,
            pointHoverRadius: 8,
            showLine: false,
            pointStyle: 'triangle',
            borderWidth: 2
        });
    }

    priceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: prices.map((_, i) => `Day ${i + 1}`),
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Stock Price Movement with Algorithm Actions',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#2c3e50',
                    bodyColor: '#2c3e50',
                    borderColor: '#e9ecef',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const point = context.raw;
                            if (point.action) {
                                return `${point.action} at price ${point.y}`;
                            }
                            return `Price: ${point.y}`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    align: 'center',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    grid: {
                        display: true,
                        color: '#e9ecef'
                    },
                    ticks: {
                        padding: 10,
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: false,
                    grid: {
                        display: true,
                        color: '#e9ecef'
                    },
                    ticks: {
                        padding: 10,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Update step visualization
    updateStepVisualization();
}

function updateStepVisualization() {
    // Update DP steps
    const dpStepsContainer = document.getElementById('dp-steps');
    dpStepsContainer.innerHTML = '';
    dpStates.forEach((state, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = `step ${state.action.toLowerCase()}`;
        stepDiv.innerHTML = `
            <div class="step-header">Day ${state.day + 1}: ${state.action}</div>
            <div class="step-details">
                Price: ${state.price}<br>
                Hold: ${state.hold.join(', ')}<br>
                Not Hold: ${state.notHold.join(', ')}
            </div>
        `;
        dpStepsContainer.appendChild(stepDiv);
    });

    // Update Greedy steps
    const greedyStepsContainer = document.getElementById('greedy-steps');
    greedyStepsContainer.innerHTML = '';
    greedyStates.forEach((state, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = `step ${state.action.toLowerCase()}`;
        stepDiv.innerHTML = `
            <div class="step-header">Day ${state.day + 1}: ${state.action}</div>
            <div class="step-details">
                Price: ${state.price}<br>
                Buy Price: ${state.buyPrice}<br>
                Current Profit: ${state.profit}<br>
                Transactions: ${state.transactions}
            </div>
        `;
        greedyStepsContainer.appendChild(stepDiv);
    });
}

function calculateProfits() {
    // Get input values
    const pricesInput = document.getElementById('prices').value;
    const k = parseInt(document.getElementById('k').value);
    const fee = parseInt(document.getElementById('fee').value);

    // Validate prices input
    if (!pricesInput.trim()) {
        alert('Please enter stock prices');
        return;
    }

    // Check for invalid characters in prices input
    const invalidChars = /[^0-9,\s]/;
    if (invalidChars.test(pricesInput)) {
        alert('Please enter only numbers separated by commas. Letters, emojis, and special symbols are not allowed.');
        return;
    }

    // Parse prices and validate each price
    const prices = pricesInput.split(',').map(price => {
        const trimmedPrice = price.trim();
        if (!trimmedPrice) {
            alert('Empty price values are not allowed');
            return null;
        }
        const numPrice = parseInt(trimmedPrice);
        if (isNaN(numPrice)) {
            alert('Invalid price value: ' + trimmedPrice);
            return null;
        }
        if (numPrice <= 0) {
            alert('Prices must be greater than 0');
            return null;
        }
        return numPrice;
    });

    // Check if any price was invalid
    if (prices.includes(null)) {
        return;
    }

    // Validate k (maximum transactions)
    if (isNaN(k) || k <= 0) {
        alert('Maximum transactions (k) must be a positive number');
        return;
    }

    // Validate fee
    if (isNaN(fee) || fee < 0) {
        alert('Transaction fee must be a non-negative number');
        return;
    }

    // Calculate results
    const dpResult = dpStockProfit(prices, k, fee);
    const greedyResult = greedyStockProfit(prices, k, fee);

    // Update results display
    document.getElementById('dp-result').textContent = dpResult;
    document.getElementById('greedy-result').textContent = greedyResult;

    // Update visualizations
    updateVisualization();
}

// Initialize with default values
calculateProfits(); 