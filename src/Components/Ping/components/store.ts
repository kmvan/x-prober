import { configure, makeAutoObservable } from 'mobx';
import type {
  ServerToBrowserPingItemProps,
  ServerToBrowserPingProps,
} from '../typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  isPing = false;
  isPingServerToBrowser = false;
  isPingServerToServer = false;
  serverToBrowserPingItems: ServerToBrowserPingItemProps[] = [];
  serverToServerPingItems: ServerToBrowserPingProps[] = [];
  constructor() {
    makeAutoObservable(this);
  }
  setIsPing = (isPing: boolean) => {
    this.isPing = isPing;
  };
  setIsPingServerToBrowser = (isPingServerToBrowser: boolean) => {
    this.isPingServerToBrowser = isPingServerToBrowser;
  };
  setIsPingServerToServer = (isPingServerToServer: boolean) => {
    this.isPingServerToServer = isPingServerToServer;
  };
  setServerToBrowserPingItems = (
    serverToBrowserPingItems: ServerToBrowserPingItemProps[]
  ) => {
    this.serverToBrowserPingItems = serverToBrowserPingItems;
  };
  setServerToServerPingItems = (
    serverToServerPingItems: ServerToBrowserPingProps[]
  ) => {
    this.serverToServerPingItems = serverToServerPingItems;
  };
  addServerToBrowserPingItem = (
    serverToBrowserPingItem: ServerToBrowserPingItemProps
  ) => {
    this.serverToBrowserPingItems.push(serverToBrowserPingItem);
  };
  addServerToServerPingItem = (
    serverToServerPingItem: ServerToBrowserPingProps
  ) => {
    this.serverToServerPingItems.push(serverToServerPingItem);
  };
}
export const PingStore = new Main();
