#include <iostream>
#include <vector>
#include <climits>
using namespace std;

int dpStockProfit(const vector<int>& prices, int k, int fee) {
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
}

int greedyStockProfit(const vector<int>& prices, int k, int fee) {
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
}

int main() {
    vector<int> prices = { 1, -3, 2, 8, 4, 9 };
    int k = 2;
    int fee = 2;

    // Filter out non-positive prices
    vector<int> cleanedPrices;
    for (int price : prices) {
        if (price > 0)
            cleanedPrices.push_back(price);
    }

    // Now use the cleanedPrices in both approaches
    int result1 = dpStockProfit(cleanedPrices, k, fee);
    cout << "Maximum Profit (Dynamic Programming): " << result1 << endl;

    int result2 = greedyStockProfit(cleanedPrices, k, fee);
    cout << "Maximum Profit (Greedy): " << result2 << endl;

    return 0;
    int n, k, fee;

    // Input: number of days
    while (true) {
        cout << "Enter number of days (must be > 0): ";
        cin >> n;
        if (n > 0) break;
        cout << "Invalid input: number of days must be greater than 0.\n";
    }

    // Input: prices
    vector<int> prices(n);
    for (int i = 0; i < n; ++i) {
        while (true) {
            cout << "Enter price for day " << (i + 1) << " (must be > 0): ";
            cin >> prices[i];
            if (prices[i] > 0) break;
            cout << "Invalid input: price must be greater than 0.\n";
        }
    }

    // Input: transaction limit (k)
    while (true) {
        cout << "Enter maximum number of transactions (k, must be greater than 0): ";
        cin >> k;
        if (k > 0) break;
        cout << "Invalid input: transactions must be greater than 0.\n";
    }

    // Input: transaction fee
    while (true) {
        cout << "Enter transaction fee (must be >= 0): ";
        cin >> fee;
        if (fee >= 0) break;
        cout << "Invalid input: fee must be 0 or greater.\n";
    }

    // Compute and display results
    int result1 = dpStockProfit(prices, k, fee);
    cout << "\nMaximum Profit (Dynamic Programming): " << result1 << endl;

    int result2 = greedyStockProfit(prices, k, fee);
    cout << "Maximum Profit (Greedy): " << result2 << endl;

    return 0;
}