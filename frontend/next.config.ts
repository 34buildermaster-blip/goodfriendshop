import type { NextConfig } from "next";

const isGithubPages = process.env.GITHUB_PAGES === "true";
const repositoryName = process.env.GITHUB_REPOSITORY?.split("/")[1] ?? "";
const isUserSite = repositoryName.endsWith(".github.io");
const basePath = isGithubPages && !isUserSite ? `/${repositoryName}` : "";
const frontendRoot = process.cwd();

const nextConfig: NextConfig = {
  turbopack: {
    root: frontendRoot,
  },
  ...(isGithubPages
    ? {
        output: "export" as const,
        basePath,
        assetPrefix: basePath,
        trailingSlash: true,
      }
    : {}),
  images: {
    unoptimized: true,
  },
  env: {
    NEXT_PUBLIC_BASE_PATH: basePath,
  },
};

export default nextConfig;
