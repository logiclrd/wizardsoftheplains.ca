using System.Collections.Generic;
using System.Diagnostics;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.StaticFiles;

namespace Website
{
	public class Program
	{
		static void Main(string[] args)
		{
			var builder = WebApplication.CreateBuilder(
				new WebApplicationOptions()
				{
					WebRootPath = "Static",
					Args = args,
				});

			var app = builder.Build();

			app.Urls.Add("http://localhost:5000/");

			app.UseDefaultFiles();
			app.UseStaticFiles(
				new StaticFileOptions()
				{
					ContentTypeProvider =
						new FileExtensionContentTypeProvider(
							new Dictionary<string, string>()
							{
								{ ".html", "text/html" },
								{ ".css", "text/css" },
								{ ".woff", "font/woff" },
								{ ".woff2", "font/woff2" },
								{ ".js", "text/javascript" },
								{ ".jpg", "image/jpeg" },
								{ ".png", "image/png" },
								{ ".svg", "image/svg+xml" },
								{ ".mp4", "video/mp4" },
								{ ".webm", "video/webm" },
								{ ".mov", "video/quicktime" },
								{ ".json", "application/json" }
							})
				});

			app.Run();
		}
	}
}
