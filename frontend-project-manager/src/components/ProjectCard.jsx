import { ClipboardList } from 'lucide-react';

export default function ProjectCard() {
  return (
    <div className="bg-orange-400 text-white rounded p-4">
      <div className="flex items-center gap-2 mb-2">
        <ClipboardList className="w-5 h-5" />
        <h2 className="text-lg font-semibold">Project</h2>
      </div>
      <div className="bg-white text-gray-800 rounded p-3 space-y-1">
        <p className="font-medium">CODESHOP</p>
        <p className="text-sm text-gray-600">
          Create a web, to buy mod GTA V. Payment must use Dana/Paypal/Steam
        </p>
        <div className="flex justify-between items-center mt-2">
          <span className="text-xs px-2 py-0.5 rounded bg-red-100 text-red-800">On create</span>
          <div className="flex -space-x-1">
            {[...Array(4)].map((_, idx) => (
              <img key={idx} src={`https://i.pravatar.cc/20?img=${idx}`} className="rounded-full border w-6 h-6" />
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
