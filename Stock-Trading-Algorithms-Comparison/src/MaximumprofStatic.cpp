#include <iostream>
#include <vector>
#include <climits>
using namespace std;

int dpStockProfit(const vector<int>& prices, int k, int fee) { // Why passed by reference: To prevent manipulation in the prices
    int n = prices.size();
    if (n == 0 || k == 0) return 0;

    // hold[t]: max profit after t transactions, currently holding a stock
    // notHold[t]: max profit after t transactions, not holding any stock
    vector<int> hold(k + 1, INT_MIN); // (size, initial value)
    vector<int> notHold(k + 1, 0);   // (size, initial value)

    // Initialization for day 0
    for (int t = 0; t <= k; ++t) 
        hold[t] = -prices[0]; // Assume we bought on day 0 for each transaction slot

    // Iterate over each day and update states
    for (int i = 1; i < n; ++i) {
        for (int t = 1; t <= k; ++t) {
            // If we sell today, we're transitioning from holding to not holding
            notHold[t] = max(notHold[t], hold[t] + prices[i] - fee);

            // If we buy today, we must have been not holding with one fewer transaction
            hold[t] = max(hold[t], notHold[t - 1] - prices[i]);
        }
    }

    return notHold[k]; // Final profit must be in the not holding state
}

int greedyStockProfit(const vector<int>& prices, int k, int fee) { // Why passed by reference: To prevent manipulation in the prices
    int n = prices.size();
    if (n == 0 || k == 0) return 0;

    int profit = 0;
    int transactions = 0;
    int buyPrice = INT_MAX;

    for (int price : prices) {
        
        if (price < 0) continue; // Skip negative prices

        // Always track the lowest effective buy price
        buyPrice = min(buyPrice, price);

        // If selling now is profitable (after fee), then sell
        if (price - buyPrice > fee) {
            profit += price - buyPrice - fee;
            transactions++;
            if (transactions >= k) break;

            // Reset buyPrice to simulate rebuying after sale
            buyPrice = price - fee;
        }
    }

    return profit;
}

int main() {
    vector<int> prices = { 2, 4, 3, 10, 1 , 7, 8, 9 };
    int k = 2;
    int fee = 2;

    int result1 = dpStockProfit(prices, k, fee);
    cout << "Maximum Profit (Dynamic Programming): " << result1 << endl;
    

    int result = greedyStockProfit(prices, k, fee);
    cout << "Maximum Profit (Greedy): " << result << endl;

    return 0;
}



