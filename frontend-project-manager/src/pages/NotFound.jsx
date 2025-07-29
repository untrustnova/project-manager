// src/pages/NotFound.jsx
import { useNavigate } from 'react-router-dom';
import { AlertTriangle, ArrowLeft, Home } from 'lucide-react';

export default function NotFound() {
  const navigate = useNavigate();

//   // Option 1: Minimal Design
//   const MinimalNotFound = () => (
//     <div className="flex items-center justify-center h-screen">
//       <div className="text-center">
//         <h1 className="text-4xl font-bold text-gray-800">404</h1>
//         <p className="mt-2 text-gray-600">Page not found</p>
//         <button
//           onClick={() => navigate('/')}
//           className="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
//         >
//           Go Home
//         </button>
//       </div>
//     </div>
//   );

  // Option 2: Illustration Design (using SVG)
//   const IllustrationNotFound = () => (
//     <div className="flex flex-col items-center justify-center h-screen p-4">
//       <svg
//         className="w-64 h-64 text-gray-400"
//         fill="none"
//         stroke="currentColor"
//         viewBox="0 0 24 24"
//         xmlns="http://www.w3.org/2000/svg"
//       >
//         <path
//           strokeLinecap="round"
//           strokeLinejoin="round"
//           strokeWidth={1}
//           d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
//         />
//       </svg>
//       <h1 className="mt-6 text-2xl font-bold text-gray-800">Oops! Lost in Space?</h1>
//       <p className="mt-2 text-gray-600 text-center max-w-md">
//         The page you're looking for doesn't exist or has been moved.
//       </p>
//       <div className="mt-6 flex gap-3">
//         <button
//           onClick={() => navigate(-1)}
//           className="flex items-center gap-2 px-4 py-2 border rounded text-gray-700 hover:bg-gray-100"
//         >
//           <ArrowLeft className="w-5 h-5" /> Go Back
//         </button>
//         <button
//           onClick={() => navigate('/')}
//           className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
//         >
//           <Home className="w-5 h-5" /> Home
//         </button>
//       </div>
//     </div>
//   );

  // Option 3: Detailed Error Design
  const DetailedNotFound = () => (
    <div className="flex flex-col items-center justify-center min-h-screen p-6 bg-gray-50">
      <div className="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <div className="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full">
          <AlertTriangle className="w-8 h-8 text-red-600" />
        </div>
        <div className="mt-4 text-center">
          <h1 className="text-2xl font-bold text-gray-900">404 Not Found</h1>
          <p className="mt-2 text-gray-600">
            We couldn't find the page you're looking for. It might have been moved or deleted.
          </p>
          <div className="mt-6">
            <div className="grid grid-cols-1 gap-4">
              <button
                onClick={() => navigate('/')}
                className="w-full px-4 py-2 text-base font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Return to Homepage
              </button>
              <button
                onClick={() => navigate(-1)}
                className="w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Go Back
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  // Choose which design to render
  return <DetailedNotFound />;
  
  // Alternatively, you could make this configurable:
  // const design = 'detailed'; // 'minimal' | 'illustration' | 'detailed'
  // switch(design) {
  //   case 'minimal': return <MinimalNotFound />;
  //   case 'illustration': return <IllustrationNotFound />;
  //   default: return <DetailedNotFound />;
  // }
}