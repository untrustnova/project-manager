// EmployeeCardPerf.jsx
export default function EmployeeCardPerf({ data }) {
  const { name, role, avatar, projectCount, tasksDone, leave, workHour } = data;

  const isOverwork = workHour > 100;
  const workColor = isOverwork ? 'bg-red-600' : 'bg-blue-900';

  return (
    <div className="bg-white border border-neutral-200 rounded-xl p-5 shadow-sm hover:shadow-md transition duration-200">
      {/* Header */}
      <div className="flex items-center gap-4 mb-4">
        <img
          src={avatar}
          alt={name}
          className="w-12 h-12 rounded-full object-cover border border-neutral-200"
        />
        <div>
          <h3 className="font-semibold text-base">{name}</h3>
          <p className="text-sm text-gray-500">{role}</p>
        </div>
      </div>

      {/* Metrics */}
      <div className="grid grid-cols-3 gap-2 text-center text-sm font-medium mb-4">
        <div>
          <p className="text-gray-500">Project</p>
          <p className="mt-1 text-black">{projectCount}</p>
        </div>
        <div>
          <p className="text-gray-500">Tasks done</p>
          <p className="mt-1 text-black">{tasksDone}</p>
        </div>
        <div>
          <p className="text-gray-500">Leave entitlement</p>
          <p className="mt-1 text-black">{leave}</p>
        </div>
      </div>

      {/* Work Hours */}
      <div>
        <p className="text-sm text-gray-600 mb-1">Work hours</p>
        <div className="relative w-full bg-gray-200 rounded-full h-3">
          <div
            className={`absolute left-0 top-0 h-3 rounded-full ${workColor}`}
            style={{ width: `${Math.min(workHour, 100)}%` }}
          />
        </div>
        <div className="flex justify-between text-xs text-gray-500 mt-1">
          <span>{workHour}%</span>
          {isOverwork && <span className="text-red-600 font-medium">⚠ Over work</span>}
        </div>
      </div>
    </div>
  );
}
