#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;

struct Edge {
    int src, weight, dest;
};

class Graph {
    int V; // Number of vertices
    vector<vector<pair<int, int>>> adj; // Adjacency list (pairs of destination vertex and weight)
    vector<Edge> edges; // List of edges

public:
    Graph(int V);
    void Add_Edge(int src, int dest, int weight);  // Add an edge to graph
    void Remove_Edge(int src, int dest); // Remove edge from graph
    void Insert_Edge(int src, int dest, int weight); // Insert edge after checking for cycle
    void Delete_Edge(int src, int dest); // Delete edge dynamically
    void display_MST();
    void kruskalMST();
    bool cycle_detection(int src, int dest); // Check if adding a new edge will form a cycle
    int find_Set(vector<int>& parent, int i);
    void union_Sets(vector<int>& parent, vector<int>& rank, int x, int y);
};

Graph::Graph(int V) {
    this->V = V;
    adj.resize(V);  // Resize adjacency list based on the number of vertices
}

void Graph::Add_Edge(int src, int dest, int weight) {
    adj[src].push_back(make_pair(dest, weight));  // Add edge to adjacency list
    adj[dest].push_back(make_pair(src, weight));  // Because it's an undirected graph
    edges.push_back({src, dest, weight}); // Add edge to list of edges
}

void Graph::Remove_Edge(int src, int dest) {
    // Remove edge from adjacency list
    adj[src].erase(remove_if(adj[src].begin(), adj[src].end(),
        [dest](const pair<int, int>& p) { return p.first == dest; }), adj[src].end());
    adj[dest].erase(remove_if(adj[dest].begin(), adj[dest].end(),
        [src](const pair<int, int>& p) { return p.first == src; }), adj[dest].end());

    // Remove edge from the list of edges
    edges.erase(remove_if(edges.begin(), edges.end(),
        [src, dest](const Edge& e) { return (e.src == src && e.dest == dest) || (e.src == dest && e.dest == src); }), edges.end());
}

void Graph::Insert_Edge(int src, int dest, int weight) {
    // Check if adding this edge will form a cycle
    if (cycle_detection(src, dest)) {
        cout << "Adding this edge would form a cycle. Edge not added.\n";
        return;
    }

    // If no cycle, add the edge to the graph
    Add_Edge(src, dest, weight);
    cout << "Edge " << src << " -- " << dest << " with weight " << weight << " added.\n";
}

void Graph::Delete_Edge(int src, int dest) {
    // Remove the edge from the graph if it exists
    auto it = find_if(edges.begin(), edges.end(),
        [src, dest](const Edge& e) { return (e.src == src && e.dest == dest) || (e.src == dest && e.dest == src); });

    if (it == edges.end()) {
        cout << "Edge " << src << " -- " << dest << " does not exist.\n";
        return;
    }

    // Remove the edge from the adjacency list and edges list
    Remove_Edge(src, dest);
    cout << "Edge " << src << " -- " << dest << " removed.\n";
}

void Graph::display_MST() {
    for (int v = 0; v < V; ++v) {
        cout << "Vertex " << v << ":";
        for (const auto& x : adj[v]) {
            cout << " -> " << x.first << " (weight: " << x.second << ")";
        }
        cout << endl;
    }
}

int Graph::find_Set(vector<int>& parent, int i) {
    if (parent[i] != i) {
        parent[i] = find_Set(parent, parent[i]);
    }
    return parent[i];
}

void Graph::union_Sets(vector<int>& parent, vector<int>& rank, int a, int b) {
    int rootA = find_Set(parent, a);
    int rootB = find_Set(parent, b);

    if (rank [rootA] < rank[rootB]) {
        parent[rootA] = rootB;
    }
    else if (rank[rootA] > rank[rootB]) {
        parent[rootB] = rootA;
    }
    else {
        parent[rootB] = rootA;
        rank[rootA]++;
    }
}

bool Graph::cycle_detection(int src, int dest) {
    vector<int> parent(V);
    vector<int> rank(V, 0);

    // Initialize Union-Find for the existing graph
    for (int v = 0; v < V; ++v) {
        parent[v] = v;
    }

    // Union-Find for all the existing edges in the graph
    for (auto& edge : edges) {
        int x = find_Set(parent, edge.src);
        int y = find_Set(parent, edge.dest);

        if (x != y) {
            union_Sets(parent, rank, x, y);
        }
    }

    // Now check if adding the new edge will form a cycle
    int x = find_Set(parent, src);
    int y = find_Set(parent, dest);

    return x == y; // If they are already in the same set, adding the edge will form a cycle.
}

void Graph::kruskalMST() {
    vector<Edge> result; // To store the resultant MST
    int e = 0; // Count of edges in MST
    int i = 0; // Initial index of sorted edges

    // Step 1: Sort all the edges in non-decreasing order of their weight
    sort(edges.begin(), edges.end(), [](Edge a, Edge b) {
        return a.weight < b.weight;
    });

    // Create V subsets with single elements
    vector<int> parent(V);
    vector<int> rank(V, 0);
    for (int v = 0; v < V; ++v) {
        parent[v] = v;
    }

    // Number of edges to be taken is equal to V-1
    while (e < V - 1 && i < edges.size()) {
        // Step 2: Pick the smallest edge and increment the index
        Edge nextEdge = edges[i++];

        int x = find_Set(parent, nextEdge.src);
        int y = find_Set(parent, nextEdge.dest);

        // If including this edge does not cause a cycle
        if (x != y) {
            result.push_back(nextEdge);
            union_Sets(parent, rank, x, y);
            e++;
        }
    }

    // Print the edges of the MST
    cout << "Edges in the Minimum Spanning Tree:\n";
    int minimumCost = 0;
    for (const auto& edge : result) {
        cout << edge.src << " -- " << edge.dest << " == " << edge.weight << endl;
        minimumCost += edge.weight;
    }
    cout << "Total weight of MST: " << minimumCost << endl;
}

int main() {
    int V, E;
    cout << "Enter the number of vertices: ";
    cin >> V;

    Graph graph(V);

    cout << "Enter the number of edges: ";
    cin >> E;

    cout << "Enter each edge (source destination weight):\n";
    for (int i = 0; i < E; ++i) {
        int src, dest, weight;
        cin >> src >> dest >> weight;
        graph.Add_Edge(src, dest, weight);
    }

    cout << "\nGraph representation:\n";
    graph.display_MST();

    // Allow the user to insert or delete edges dynamically
    int choice;
    do {
        cout << "Choose an operation:"<<endl;
        cout << "1. Insert an edge: " << endl;
        cout << "2. Delete an edge: " << endl;
        cout << "3. No change: " << endl;
        cout << "Enter your choice : ";
        cin >> choice;

        if (choice == 1) {
            int src, dest, weight;
            cout << "Enter the Edge (weight & source  & destination): ";
            cin >> weight >> src >> dest ;
            
            // Check if adding this edge will form a cycle
            if (graph.cycle_detection(src, dest)) {
                cout << "Adding this edge will form a cycle." << endl;
            } else {
                graph.Insert_Edge(weight,src, dest); // If no cycle, add the edge
            }
        } else if (choice == 2) {
            int src, dest;
            cout << "Enter the edge to delete (source destination): "<<endl;
            cin >> src >> dest;
            graph.Delete_Edge(src, dest);
        }
    } while (choice != 3);

    cout << "Final graph representation:" << endl;
    graph.display_MST();

    cout << "Minimum Spanning Tree (MST): " << endl;
    graph.kruskalMST();

    return 0;
}