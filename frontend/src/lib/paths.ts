export function assetPath(path: string) {
  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }

  const basePath = process.env.NEXT_PUBLIC_BASE_PATH ?? "";
  const normalizedPath = path.startsWith("/") ? path : `/${path}`;

  return `${basePath}${normalizedPath}`;
}
