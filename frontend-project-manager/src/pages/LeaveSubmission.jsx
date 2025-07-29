// src/pages/LeaveSubmission.jsx
import { useState } from 'react';

export default function LeaveSubmission() {
  const [form, setForm] = useState({
    category: '',
    startDate: '',
    endDate: '',
    description: '',
    bringLaptop: '',
    canBeContacted: '',
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = () => {
    if (!form.category || !form.startDate || !form.endDate) {
      alert('Please fill all required fields');
      return;
    }
    console.log('Leave submitted:', form);
    alert('Leave submission sent!');
  };

  const handleCancel = () => {
    setForm({
      category: '',
      startDate: '',
      endDate: '',
      description: '',
      bringLaptop: '',
      canBeContacted: '',
    });
  };

  return (
    <div className="p-6 max-w-4xl mx-auto">
      <div className="bg-white p-8 rounded-xl shadow-sm border border-neutral-200">
        <h2 className="text-xl font-semibold mb-6">Leave Submission</h2>

        {/* Leave Category */}
        <div className="mb-5">
          <select
            name="category"
            value={form.category}
            onChange={handleChange}
            className="w-full border border-neutral-200 px-4 py-2 rounded bg-gray-100 text-gray-700"
          >
            <option value="">Leave Category</option>
            <option value="Sick">Sick</option>
            <option value="Annual">Annual</option>
            <option value="Emergency">Emergency</option>
          </select>
        </div>

        {/* Date */}
        <div className="mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
          <input
            name="startDate"
            type="date"
            value={form.startDate}
            onChange={handleChange}
            className="w-full border border-neutral-200 px-4 py-2 rounded bg-gray-100"
            placeholder="Start Date"
          />
          <input
            name="endDate"
            type="date"
            value={form.endDate}
            onChange={handleChange}
            className="w-full border border-neutral-200 px-4 py-2 rounded bg-gray-100"
            placeholder="End Date"
          />
        </div>

        {/* Description */}
        <div className="mb-5">
          <textarea
            name="description"
            value={form.description}
            onChange={handleChange}
            rows={4}
            placeholder="Description"
            className="w-full border border-neutral-200 px-4 py-2 rounded bg-gray-100"
          />
        </div>

        {/* Bring Laptop */}
        <div className="mb-5">
          <p className="text-sm font-medium mb-2">
            Do you bring laptop? <span className="text-gray-400">(if there is a super urgent matter)</span>
          </p>
          <div className="bg-gray-100 rounded-lg px-4 py-2 flex gap-6">
            <label className="flex items-center gap-2 text-sm">
              <input
                type="radio"
                name="bringLaptop"
                value="Yes"
                checked={form.bringLaptop === 'Yes'}
                onChange={handleChange}
              />
              Yes
            </label>
            <label className="flex items-center gap-2 text-sm">
              <input
                type="radio"
                name="bringLaptop"
                value="No"
                checked={form.bringLaptop === 'No'}
                onChange={handleChange}
              />
              No
            </label>
          </div>
        </div>

        {/* Can Be Contacted */}
        <div className="mb-8">
          <p className="text-sm font-medium mb-2">
            Do you still be Contacted? <span className="text-gray-400">(if there is a super urgent matter)</span>
          </p>
          <div className="bg-gray-100 rounded-lg px-4 py-2 flex gap-6">
            <label className="flex items-center gap-2 text-sm">
              <input
                type="radio"
                name="canBeContacted"
                value="Yes"
                checked={form.canBeContacted === 'Yes'}
                onChange={handleChange}
              />
              Yes
            </label>
            <label className="flex items-center gap-2 text-sm">
              <input
                type="radio"
                name="canBeContacted"
                value="No"
                checked={form.canBeContacted === 'No'}
                onChange={handleChange}
              />
              No
            </label>
          </div>
        </div>

        {/* Buttons */}
        <div className="flex justify-center gap-4">
          <button
            onClick={handleCancel}
            className="border border-neutral-200 border border-neutral-200-gray-400 px-6 py-2 rounded hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            onClick={handleSubmit}
            className="bg-black text-white px-6 py-2 rounded hover:opacity-90"
          >
            Submit
          </button>
        </div>
      </div>
    </div>
  );
}
