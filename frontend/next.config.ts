import type { NextConfig } from "next";

const isGithubPages = process.env.GITHUB_PAGES === "true";
const repositoryName = process.env.GITHUB_REPOSITORY?.split("/")[1] ?? "";
const isUserSite = repositoryName.endsWith(".github.io");
const basePath = isGithubPages && !isUserSite ? `/${repositoryName}` : "";

const nextConfig: NextConfig = {
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
};

export default nextConfig;
