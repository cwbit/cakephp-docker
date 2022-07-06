import Browser, { BrowserConfig } from './lib/browser';
import Service, { ServiceConfig, ServiceReferer } from './lib/service';
export declare class Bonjour {
    private server;
    private registry;
    constructor(opts?: ServiceConfig | undefined);
    publish(opts: ServiceConfig): Service;
    unpublishAll(callback?: CallableFunction | undefined): void;
    find(opts?: BrowserConfig | undefined, onup?: (...args: any[]) => void): Browser;
    findOne(opts: BrowserConfig | undefined, timeout: number | undefined, callback: CallableFunction): Browser;
    destroy(): void;
}
export { Service, ServiceReferer, Browser };
export default Bonjour;
