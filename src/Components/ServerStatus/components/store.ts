import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { ServerStatusPollDataProps } from './typings';

configure({
  enforceActions: 'observed',
});
class Main {
  pollData: ServerStatusPollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: ServerStatusPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
  get sysLoad(): ServerStatusPollDataProps['sysLoad'] {
    return this.pollData?.sysLoad || [0, 0, 0];
  }
  get cpuUsage(): ServerStatusPollDataProps['cpuUsage'] {
    return (
      this.pollData?.cpuUsage ?? {
        usage: 0,
        idle: 100,
        sys: 0,
        user: 0,
      }
    );
  }
  get memRealUsage(): ServerStatusPollDataProps['memRealUsage'] {
    return (
      this.pollData?.memRealUsage ?? {
        max: 0,
        value: 0,
      }
    );
  }
  get memCached(): ServerStatusPollDataProps['memCached'] {
    return (
      this.pollData?.memCached ?? {
        max: 0,
        value: 0,
      }
    );
  }
  get memBuffers(): ServerStatusPollDataProps['memBuffers'] {
    return (
      this.pollData?.memBuffers ?? {
        max: 0,
        value: 0,
      }
    );
  }
  get swapUsage(): ServerStatusPollDataProps['swapUsage'] {
    return (
      this.pollData?.swapUsage ?? {
        max: 0,
        value: 0,
      }
    );
  }
  get swapCached(): ServerStatusPollDataProps['swapCached'] {
    return (
      this.pollData?.swapCached ?? {
        max: 0,
        value: 0,
      }
    );
  }
}
export const ServerStatusStore = new Main();
