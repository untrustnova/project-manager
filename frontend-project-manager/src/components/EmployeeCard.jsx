export default function EmployeeCard({ data, tab }) {
  return (
    <div className="bg-white p-4 rounded shadow text-sm">
      <div className="flex items-center gap-3 mb-2">
        <img src={data.img} alt="avatar" className="rounded-full w-10 h-10" />
        <div>
          <h3 className="font-semibold text-gray-800">{data.name}</h3>
          <p className="text-gray-500 text-xs">{data.role || `${data.days} day off`}</p>
        </div>
      </div>

      {tab === "Absent" && (
        <>
          <p className="font-medium text-gray-700">{data.reason}</p>
          <p className="text-xs text-gray-500">{data.note}</p>
        </>
      )}

      {tab !== "Absent" && data.task && (
        <p className="text-gray-700 mb-2">
          <span className="font-medium">Working on:</span><br />{data.task}
        </p>
      )}

      {data.status && (
        <div className="inline-block text-xs px-2 py-0.5 rounded bg-green-100 text-green-700">
          {data.status}
        </div>
      )}
    </div>
  );
}
