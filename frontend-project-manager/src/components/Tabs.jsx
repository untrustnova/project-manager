// src/components/Tabs.jsx
export default function Tabs({ activeTab, onChange }) {
  const tabs = ['Ready', 'Stand by', 'Not ready', 'Complete', 'Absent'];

  return (
    <div className="flex gap-2">
      {tabs.map((tab) => (
        <button
          key={tab}
          onClick={() => onChange(tab)}
          className={`px-4 py-1.5 rounded-full text-sm font-medium ${
            activeTab === tab ? 'bg-black text-white' : 'bg-gray-100 text-gray-700'
          }`}
        >
          {tab}
        </button>
      ))}
    </div>
  );
}
