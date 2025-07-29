// src/components/ActivityChart.jsx
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Filler,
} from 'chart.js';
import { Line } from 'react-chartjs-2';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Filler
);

export default function ActivityChart() {
  const data = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
    datasets: [
      {
        label: 'Hours Worked',
        data: [40, 28, 34, 52, 27],
        fill: true,
        backgroundColor: 'rgba(59, 130, 246, 0.1)', // blue-500 w/ 10%
        borderColor: 'rgba(59, 130, 246, 1)', // blue-500
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 6,
      },
    ],
  };

  const options = {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: ctx => `${ctx.parsed.y}h` } }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => `${value}h`,
        },
        grid: { display: true }
      },
      x: {
        grid: { display: false }
      }
    }
  };

  return (
    <div className="bg-white rounded p-4 shadow">
      <h2 className="text-lg font-semibold text-gray-800 mb-2">Activity</h2>
      <Line data={data} options={options} />
    </div>
  );
}
