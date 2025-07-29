import { useNavigate } from 'react-router-dom';
import { Plus, Trash2, Search, ChevronDown, Pencil } from 'lucide-react';

const dummyData = [
  { id: 1, name: "Ahmad Wahid", division: "Analis", email: "Ahmad@email.com", role: "User" },
  { id: 2, name: "Nur Wahid Alfiansyah", division: "Analis", email: "Ahmad@email.com", role: "User" },
  { id: 3, name: "Rahmat Irawan", division: "Backend Developer", email: "Ahmad@email.com", role: "User" },
  { id: 4, name: "Jesse Pinkman", division: "Front End Developer", email: "Ahmad@email.com", role: "Admin" },
  { id: 5, name: "Kobe Bryant", division: "UI/UX Designer", email: "Ahmad@email.com", role: "User" },
];

export default function EmployeeList() {
  const navigate = useNavigate();

  return (
    <div className="p-6">
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        {/* Top Controls */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
          {/* Left Filters */}
          <div className="flex flex-wrap gap-3">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
              <input
                type="text"
                placeholder="Search SDM"
                className="pl-10 pr-4 py-2 border rounded bg-white text-sm"
              />
            </div>

            <div className="relative">
              <select className="appearance-none border pl-4 pr-8 py-2 rounded bg-white text-sm text-gray-700">
                <option value="">Filter by divisi</option>
                <option value="Analis">Analis</option>
                <option value="Backend Developer">Backend Developer</option>
                <option value="Front End Developer">Front End Developer</option>
                <option value="UI/UX Designer">UI/UX Designer</option>
              </select>
              <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4 pointer-events-none" />
            </div>
          </div>

          {/* Right - Add New SDM */}
          <button
            onClick={() => navigate("/employee/create")}
            className="bg-black text-white text-sm px-4 py-2 rounded flex items-center gap-2 self-start md:self-auto"
          >
            <Plus className="w-4 h-4" /> Add New SDM
          </button>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-sm border-t">
            <thead className="bg-gray-100 text-gray-700">
              <tr>
                <th className="text-left px-4 py-3 font-medium">#</th>
                <th className="text-left px-4 py-3 font-medium">Username</th>
                <th className="text-left px-4 py-3 font-medium">Divisi</th>
                <th className="text-left px-4 py-3 font-medium">Email</th>
                <th className="text-left px-4 py-3 font-medium">Role</th>
                <th className="text-left px-4 py-3 font-medium">Action</th>
              </tr>
            </thead>
            <tbody>
              {dummyData.map((item, idx) => (
                <tr key={item.id} className="border-t hover:bg-gray-50 transition">
                  <td className="px-4 py-3">{idx + 1}</td>
                  <td className="px-4 py-3">{item.name}</td>
                  <td className="px-4 py-3">{item.division}</td>
                  <td className="px-4 py-3">{item.email}</td>
                  <td className="px-4 py-3">{item.role}</td>
                  <td className="px-4 py-3 flex gap-2">
                    {/* Edit button */}
                    <button
                      onClick={() => navigate(`/employee/${item.id}/edit`)}
                      className="bg-blue-500 text-white text-xs px-3 py-1 rounded flex items-center gap-1"
                    >
                      <Pencil className="w-4 h-4" /> Edit
                    </button>

                    {/* Delete button */}
                    <button className="bg-red-500 text-white text-xs px-3 py-1 rounded flex items-center gap-1">
                      <Trash2 className="w-4 h-4" /> Delete
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
