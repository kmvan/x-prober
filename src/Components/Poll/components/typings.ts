import type { ConfigProps } from '@/Components/Config/typings.ts';
import type { DatabasePollDataProps } from '@/Components/Database/components/typings.ts';
import type { DiskUsagePollDataProps } from '@/Components/DiskUsage/components/typings.ts';
import type { MyInfoPollDataProps } from '@/Components/MyInfo/components/typings.ts';
import type { NetworkStatsPollDataProps } from '@/Components/NetworkStats/components/typings.ts';
import type { NodesPollDataProps } from '@/Components/Nodes/components/typings.ts';
import type { PhpExtensionsPollDataProps } from '@/Components/PhpExtensions/components/typings.ts';
import type { PhpInfoPollDataProps } from '@/Components/PhpInfo/components/typings.ts';
import type { ServerInfoPollDataProps } from '@/Components/ServerInfo/components/typings.ts';
import type { ServerStatusPollDataProps } from '@/Components/ServerStatus/components/typings.ts';
import type { TemperatureSensorPollDataProps } from '@/Components/TemperatureSensor/components/typings.ts';
import type { UserConfigProps } from '@/Components/UserConfig/typings.ts';

export interface PollDataProps {
  config: ConfigProps | null;
  userConfig: UserConfigProps | null;
  database: DatabasePollDataProps | null;
  myInfo: MyInfoPollDataProps | null;
  phpInfo: PhpInfoPollDataProps | null;
  diskUsage: DiskUsagePollDataProps | null;
  networkStats: NetworkStatsPollDataProps | null;
  phpExtensions: PhpExtensionsPollDataProps | null;
  serverStatus: ServerStatusPollDataProps | null;
  serverInfo: ServerInfoPollDataProps | null;
  nodes: NodesPollDataProps | null;
  temperatureSensor: TemperatureSensorPollDataProps | null;
}
