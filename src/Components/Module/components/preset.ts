import { DatabaseLoader } from '@/Components/Database/components/loader.ts';
import { DiskUsageLoader } from '@/Components/DiskUsage/components/loader.ts';
import { MyInfoLoader } from '@/Components/MyInfo/components/loader.ts';
import { NetworkStatsLoader } from '@/Components/NetworkStats/components/loader.ts';
import { NodesLoader } from '@/Components/Nodes/components/loader.ts';
import { PhpExtensionsLoader } from '@/Components/PhpExtensions/components/loader.ts';
import { PhpInfoLoader } from '@/Components/PhpInfo/components/loader.ts';
import { PingLoader } from '@/Components/Ping/components/loader.ts';
import { ServerBenchmarkLoader } from '@/Components/ServerBenchmark/components/loader.ts';
import { ServerInfoLoader } from '@/Components/ServerInfo/components/loader.ts';
import { ServerStatusLoader } from '@/Components/ServerStatus/components/loader.ts';
import { TemperatureSensorLoader } from '@/Components/TemperatureSensor/components/loader.ts';export const ModulePreset = {
  items: [
    NodesLoader,
    TemperatureSensorLoader,
    ServerStatusLoader,
    NetworkStatsLoader,
    DiskUsageLoader,
    PingLoader,
    ServerInfoLoader,
    PhpInfoLoader,
    PhpExtensionsLoader,
    DatabaseLoader,
    MyInfoLoader,
    ServerBenchmarkLoader,
  ],
};
