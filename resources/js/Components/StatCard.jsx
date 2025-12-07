export default function StatCard({
    title,
    value,
    description,
    icon,
    colorClass = 'text-gray-900',
}) {
    return (
        <div className="overflow-hidden rounded-lg bg-white shadow">
            <div className="p-5">
                <div className="flex items-center">
                    {icon && (
                        <div className="mr-4 flex-shrink-0">
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100">
                                {icon}
                            </div>
                        </div>
                    )}
                    <div className="flex-1">
                        <p className="truncate text-sm font-medium text-gray-500">
                            {title}
                        </p>
                        <p className={`mt-1 text-3xl font-semibold ${colorClass}`}>
                            {value}
                        </p>
                        {description && (
                            <p className="mt-1 text-sm text-gray-500">
                                {description}
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
