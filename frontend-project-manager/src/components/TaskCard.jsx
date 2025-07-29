// src/components/TaskCard.jsx
export default function TaskCard() {
  return (
    <div className="bg-green-500 text-white rounded p-4 space-y-3">
      <h2 className="text-lg font-semibold">Tasks</h2>
      <div className="bg-white text-gray-800 rounded p-3">
        <p className="font-medium">Create filter to find data resource</p>
        <p className="text-sm text-gray-600">Create button and if click data will show</p>
        <span className="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-800">Low</span>
      </div>
      <div className="bg-white text-gray-800 rounded p-3">
        <p className="font-medium">Displaying and merging data</p>
        <p className="text-sm text-gray-600">Make access easier in web codelab</p>
        <span className="text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">Medium</span>
      </div>
    </div>
  );
}
