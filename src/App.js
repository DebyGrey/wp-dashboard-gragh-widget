import React, { useState, useEffect } from 'react';
import Graph from './components/Graph';


const App = () => {


  const [currentGraph, setCurrentGraph] = useState([]);

  const [graphs, setGraphs] = useState([]);

  useEffect(() => {
    fetch('http://localhost/graph/index.php/wp-json/wp/v2/graph')
      .then((res) => res.json())
      .then((data) => {
        setGraphs(data);
        setCurrentGraph(JSON.parse(data[0].data));
      })
      .catch((err) => {
        console.log(err.message);
      });
  }, []);

  return (
    <div className='graph'>
      <div className='graph-header'>
        <form>
          <select onChange={(e) => {
            const filteredGraph = graphs.filter((graph) => {
              return graph.duration === e.target.value;
            })[0];
            let data = JSON.parse(filteredGraph.data);
            setCurrentGraph(data);
            // console.log(currentGraph);
          }}>
            {graphs.map((graph) => {
              return (<option key={graph.graph_id}>{graph.duration}</option>)
            })}
          </select>
        </form>
      </div>
      <div className='graph-body'>
        <Graph data={currentGraph} />
      </div>
    </div>
  );
}
export default App;
