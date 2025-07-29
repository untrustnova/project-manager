import { FiFilter } from 'react-icons/fi'; // npm install react-icons if not installed
import EmployeeCardPerf from '../components/EmployeeCardPerf';

const employees = [
  {
    name: 'Carla Lalisa',
    role: 'Copywriter',
    avatar: '/avatars/carla.jpg',
    projectCount: 10,
    tasksDone: 210,
    leave: 2,
    workHour: 45
  },
  {
    name: 'Tony Hank',
    role: 'AI Developer',
    avatar: '/avatars/tony.jpg',
    projectCount: 10,
    tasksDone: 120,
    leave: 0,
    workHour: 100
  },
  {
    name: 'Eminem',
    role: 'Content creator',
    avatar: '/avatars/eminem.jpg',
    projectCount: 10,
    tasksDone: 210,
    leave: 2,
    workHour: 70
  },
  {
    name: 'Vladen Noer',
    role: 'Copywriter',
    avatar: '/avatars/vladen.jpg',
    projectCount: 10,
    tasksDone: 210,
    leave: 2,
    workHour: 50
  },
  {
    name: 'Darth Snoop',
    role: 'UX Designer',
    avatar: '/avatars/darth.jpg',
    projectCount: 10,
    tasksDone: 120,
    leave: 0,
    workHour: 120
  },
  {
    name: 'Freyaa',
    role: 'UI Designer',
    avatar: '/avatars/freyaa.jpg',
    projectCount: 10,
    tasksDone: 210,
    leave: 2,
    workHour: 46
  }
];

export default function Activity() {
  return (
    <div className="p-6">
      {/* Filter Button aligned left */}
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-semibold">Employee Activity</h2>
      </div>

      <div className="flex items-center mb-4">
        <button className="flex items-center gap-2 border border-gray-300 text-sm px-4 py-2 rounded bg-white hover:bg-gray-50 transition">
          <FiFilter className="text-gray-600" size={16} />
          Filter
        </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {employees.map((emp, i) => (
          <EmployeeCardPerf key={i} data={emp} />
        ))}
      </div>
    </div>
  );
}
