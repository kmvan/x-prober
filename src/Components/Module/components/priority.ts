import { BrowserBenchmarkConstants } from '@/Components/BrowserBenchmark/components/constants.ts';
import { PingConstants } from '@/Components/Ping/components/constants.ts';
import { DatabaseConstants } from '../../Database/components/constants.ts';
import { DiskUsageConstants } from '../../DiskUsage/components/constants.ts';
import { MyInfoConstants } from '../../MyInfo/components/constants.ts';
import { NetworkStatsConstants } from '../../NetworkStats/components/constants.ts';
import { NodesConstants } from '../../Nodes/components/constants.ts';
import { PhpExtensionsConstants } from '../../PhpExtensions/components/constants.ts';
import { PhpInfoConstants } from '../../PhpInfo/components/constants.ts';
import { ServerBenchmarkConstants } from '../../ServerBenchmark/components/constants.ts';
import { ServerInfoConstants } from '../../ServerInfo/components/constants.ts';
import { ServerStatusConstants } from '../../ServerStatus/components/constants.ts';
import { TemperatureSensorConstants } from '../../TemperatureSensor/components/constants.ts';
export const ModulePriority = [
  NodesConstants.id,
  TemperatureSensorConstants.id,
  ServerStatusConstants.id,
  NetworkStatsConstants.id,
  DiskUsageConstants.id,
  ServerInfoConstants.id,
  PingConstants.id,
  PhpInfoConstants.id,
  PhpExtensionsConstants.id,
  DatabaseConstants.id,
  ServerBenchmarkConstants.id,
  BrowserBenchmarkConstants.id,
  MyInfoConstants.id,
];
